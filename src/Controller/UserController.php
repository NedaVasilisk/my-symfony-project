<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/users')]
class UserController extends AbstractController
{
    public function __construct(private UserService $userService) {}

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $users = $this->userService->getAllUsers();
        return $this->json($users, Response::HTTP_OK);
    }

    #[Route('/collection', name: 'app_user_collection', methods: ['GET'])]
    public function getCollection(Request $request, UserRepository $userRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $usersData = $userRepository->getAllUsersByFilter($requestData, $itemsPerPage, $page);

        return $this->json($usersData, JsonResponse::HTTP_OK, [], ['groups' => ['user_detail', 'role_list']]);
    }

    #[Route('/create', name: 'app_user_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $user = $this->userService->createUser($requestData);
        return $this->json($user, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        return $this->json($user, Response::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, User $user): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedUser = $this->userService->updateUser($user, $requestData);
        return $this->json($updatedUser, Response::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_user_delete', methods: ['DELETE'])]
    public function delete(User $user): JsonResponse
    {
        $this->userService->deleteUser($user);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
