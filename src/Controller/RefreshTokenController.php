<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RefreshTokenController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(
        EntityManagerInterface $entityManager,
        JWTTokenManagerInterface $jwtManager
    ) {
        $this->entityManager = $entityManager;
        $this->jwtManager = $jwtManager;
    }

    #[Route('/api/token/refresh', name: 'refresh_token', methods: ['POST'])]
    public function refresh(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $refreshToken = $data['refresh_token'] ?? null;

        if (!$refreshToken) {
            return new JsonResponse(['error' => 'Refresh token is required'], Response::HTTP_BAD_REQUEST);
        }

        try {
            $payload = $this->jwtManager->parse($refreshToken);

            if (!$payload || !isset($payload['type']) || $payload['type'] !== 'refresh') {
                return new JsonResponse(['error' => 'Invalid refresh token'], Response::HTTP_UNAUTHORIZED);
            }
        } catch (Exception $e) {
            error_log('Invalid refresh token: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Invalid refresh token'], Response::HTTP_UNAUTHORIZED);
        }

        $userEmail = $payload['email'] ?? null;
        if (!$userEmail) {
            return new JsonResponse(['error' => 'Invalid refresh token payload'], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userEmail]);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_UNAUTHORIZED);
        }

        $newAccessToken = $this->jwtManager->create($user);
        $newRefreshToken = $this->jwtManager->createFromPayload($user, [
            'type' => 'refresh',
            'exp' => time() + 3600 * 24 * 7,
        ]);

        return new JsonResponse([
            'token' => $newAccessToken,
            'refresh_token' => $newRefreshToken,
        ]);
    }
}
