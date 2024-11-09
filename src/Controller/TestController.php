<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    private array $data = [
        ['id' => 1, 'name' => 'Item 1', 'description' => 'Description of Item 1'],
        ['id' => 2, 'name' => 'Item 2', 'description' => 'Description of Item 2'],
    ];

    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    #[Route('/my/{id}', name: 'app_my_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        // Пошук елемента за ID
        $item = array_filter($this->data, fn($item) => $item['id'] === $id);
        if (empty($item)) {
            return $this->json(['error' => 'Item not found'], 404);
        }

        return $this->json(array_values($item)[0]);
    }

    #[Route('/my', name: 'app_my_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $newItem = [
            'id' => count($this->data) + 1,
            'name' => $request->get('name'),
            'description' => $request->get('description'),
        ];

        $this->data[] = $newItem;

        return $this->json($newItem, 201);
    }

    #[Route('/my/{id}', name: 'app_my_update', methods: ['PUT'])]
    public function update(Request $request, int $id): JsonResponse
    {
        foreach ($this->data as &$item) {
            if ($item['id'] === $id) {
                $item['name'] = $request->get('name', $item['name']);
                $item['description'] = $request->get('description', $item['description']);
                return $this->json($item);
            }
        }

        return $this->json(['error' => 'Item not found'], 404);
    }

    #[Route('/my/{id}', name: 'app_my_delete', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        foreach ($this->data as $key => $item) {
            if ($item['id'] === $id) {
                unset($this->data[$key]);
                return $this->json(['message' => 'Item deleted']);
            }
        }

        return $this->json(['error' => 'Item not found'], 404);
    }
}
