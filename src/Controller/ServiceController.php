<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use App\Service\ServiceService;
use App\Service\ServiceValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/service')]
class ServiceController extends AbstractController
{
    #[Route('/', name: 'app_service_index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository): Response
    {
        $services = $serviceRepository->findAll();
        return $this->json($services);
    }

    #[Route('/create', name: 'app_service_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ServiceValidator $serviceValidator,
        ServiceService $serviceService
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $serviceValidator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $service = $serviceService->createOrUpdateService($data);
        $entityManager->persist($service);
        $entityManager->flush();

        return $this->json($service, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_service_show', methods: ['GET'])]
    public function show(Service $service): Response
    {
        return $this->json($service);
    }

    #[Route('/{id}/edit', name: 'app_service_edit', methods: ['PUT', 'PATCH'])]
    public function edit(
        Request $request,
        Service $service,
        EntityManagerInterface $entityManager,
        ServiceValidator $serviceValidator,
        ServiceService $serviceService
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $serviceValidator->validate($data);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $service = $serviceService->createOrUpdateService($data, $service);
        $entityManager->flush();

        return $this->json($service);
    }

    #[Route('/{id}/delete', name: 'app_service_delete', methods: ['DELETE'])]
    public function delete(Service $service, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($service);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
