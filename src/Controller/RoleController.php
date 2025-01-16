<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use App\Service\RoleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('api/roles')]
class RoleController extends AbstractController
{
    public function __construct(private RoleService $roleService) {}

    #[Route('/', name: 'app_role_index', methods: ['GET'])]
    public function index(RoleRepository $roleRepository): JsonResponse
    {
        $roles = $roleRepository->findAll();
        return $this->json($roles, Response::HTTP_OK);
    }

    #[Route('/collection', name: 'app_role_collection', methods: ['GET'])]
    public function getCollection(Request $request, RoleRepository $roleRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $rolesData = $roleRepository->getAllRolesByFilter($requestData, $itemsPerPage, $page);

        return $this->json(
            $rolesData,
            JsonResponse::HTTP_OK,
            [],
            ['groups' => ['role_detail']]
        );
    }

    #[Route('/create', name: 'app_role_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $role = $this->roleService->createRole($requestData);
        return $this->json($role, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_role_show', methods: ['GET'])]
    public function show(Role $role): JsonResponse
    {
        return $this->json($role, Response::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_role_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Role $role): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);
        $updatedRole = $this->roleService->updateRole($role, $requestData);
        return $this->json($updatedRole, Response::HTTP_OK);
    }

    #[Route('/{id}/delete', name: 'app_role_delete', methods: ['DELETE'])]
    public function delete(Role $role): JsonResponse
    {
        $this->roleService->deleteRole($role);
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
