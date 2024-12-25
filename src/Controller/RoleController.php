<?php

namespace App\Controller;

use App\Entity\Role;
use App\Form\RoleType;
use App\Repository\RoleRepository;
use App\Service\RoleService;
use App\Service\RoleValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/role')]
class RoleController extends AbstractController
{
    #[Route('/', name: 'app_role_index', methods: ['GET'])]
    public function index(RoleRepository $roleRepository): Response
    {
        $roles = $roleRepository->findAll();
        return $this->json($roles);
    }

    #[Route('/create', name: 'app_role_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        RoleValidator $roleValidator,
        RoleService $roleService
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $roleValidator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $role = $roleService->createOrUpdateRole($data);
        $entityManager->persist($role);
        $entityManager->flush();

        return $this->json($role, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_role_show', methods: ['GET'])]
    public function show(Role $role): Response
    {
        return $this->json($role);
    }

    #[Route('/{id}/edit', name: 'app_role_edit', methods: ['PUT', 'PATCH'])]
    public function edit(
        Request $request,
        Role $role,
        EntityManagerInterface $entityManager,
        RoleValidator $roleValidator,
        RoleService $roleService
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $roleValidator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $role = $roleService->createOrUpdateRole($data, $role);
        $entityManager->flush();

        return $this->json($role);
    }

    #[Route('/{id}/delete', name: 'app_role_delete', methods: ['DELETE'])]
    public function delete(Role $role, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($role);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
