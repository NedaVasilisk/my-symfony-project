<?php

namespace App\Controller;

use App\Entity\Role;
use App\Repository\RoleRepository;
use App\Service\RoleService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('api/roles')]
class RoleController extends AbstractController
{
    public function __construct(private readonly RoleService $roleService) {}

    #[Route('/', name: 'app_role_index', methods: ['GET'])]
    public function index(Request $request, RoleRepository $roleRepository): JsonResponse
    {
        $requestData = $request->query->all();
        $itemsPerPage = isset($requestData['itemsPerPage']) ? max((int)$requestData['itemsPerPage'], 1) : 10;
        $page = isset($requestData['page']) ? max((int)$requestData['page'], 1) : 1;

        $rolesData = $roleRepository->getAllRolesByFilter($requestData, $itemsPerPage, $page);

        return $this->json($rolesData, Response::HTTP_OK, [], ['groups' => ['role_detail']]);
    }

    #[Route('/', name: 'app_role_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        try {
            $this->roleService->createRole($requestData);
            return $this->json(['message' => 'Successfully created'], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_role_show', methods: ['GET'])]
    public function show(Role $role): JsonResponse
    {
        return $this->json($role, Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_role_edit', methods: ['PUT', 'PATCH'])]
    public function edit(Request $request, Role $role): JsonResponse
    {
        $requestData = json_decode($request->getContent(), true);

        try {
            $this->roleService->updateRole($role, $requestData);
            return $this->json(['message' => 'Successfully updated'], Response::HTTP_OK);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }

    #[Route('/{id}', name: 'app_role_delete', methods: ['DELETE'])]
    public function delete(Role $role): JsonResponse
    {
        try {
            $this->roleService->deleteRole($role);
            return $this->json(['message' => 'Successfully deleted'], Response::HTTP_NO_CONTENT);
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        }
    }
}
