<?php

namespace App\EventListener;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Cookie;

class AuthenticationSuccessListener
{
    private JWTTokenManagerInterface $jwtManager;

    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    #[AsEventListener(event: 'lexik_jwt_authentication.on_authentication_success')]
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof User) {
            return;
        }

        $data = $event->getData();

        $jwtToken = $data['token'] ?? null;
        if (!$jwtToken) {
            return;
        }

        $refreshToken = $this->jwtManager->createFromPayload($user, [
            'type' => 'refresh',
            'exp' => time() + 3600 * 24 * 7,
        ]);

        $event->setData([
            'token' => $jwtToken,
            'refresh_token' => $refreshToken,
        ]);

    }
}
