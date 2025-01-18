<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Service\UserService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsController]
#[Route('/api/register', name: 'app_register', methods: ['POST'])]
class RegistrationController
{
    public function __construct(
        private readonly UserService                 $userService,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly ValidatorInterface          $validator,
        private readonly EntityManagerInterface      $entityManager
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Перевірка обов'язкових полів
        if (empty($data['username']) || empty($data['password']) || empty($data['email'])) {
            return new JsonResponse(['error' => 'Username, password, and email are required.'], Response::HTTP_BAD_REQUEST);
        }

        // Створення нового користувача
        $user = new User();
        $user->setUsername($data['username']);
        $user->setEmail($data['email']);
        $user->setFirstName($data['firstName'] ?? '');
        $user->setLastName($data['lastName'] ?? '');
        $user->setCreatedAt(new DateTime());
        $user->setIsActive(true);

        // Хешування пароля
        $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // Призначення базової ролі
        $roleRepository = $this->entityManager->getRepository(Role::class);
        $role = $roleRepository->findOneBy(['roleName' => 'ROLE_USER']); // Знайти роль "ROLE_USER"
        if ($role) {
            $user->setRole($role);
        } else {
            return new JsonResponse(['error' => 'Default role "ROLE_USER" not found.'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Валідація користувача
        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string)$errors], Response::HTTP_BAD_REQUEST);
        }

        try {
            // Використання UserService для збереження користувача
            $this->userService->createUser([
                'username' => $user->getUsername(),
                'passwordHash' => $user->getPassword(),
                'email' => $user->getEmail(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
                'isActive' => $user->isActive(),
                'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            ]);

            return new JsonResponse(['message' => 'User registered successfully.'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
