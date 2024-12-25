<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Vehicle;
use App\Form\VehicleType;
use App\Repository\VehicleRepository;
use App\Service\VehicleService;
use App\Service\VehicleValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vehicle')]
class VehicleController extends AbstractController
{
    #[Route('/', name: 'app_vehicle_index', methods: ['GET'])]
    public function index(VehicleRepository $vehicleRepository): Response
    {
        $vehicles = $vehicleRepository->findAll();
        return $this->json($vehicles, 200, [], ['groups' => ['vehicle_detail', 'customer_list']]);
    }

    #[Route('/create', name: 'app_vehicle_new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        VehicleService $vehicleService,
        VehicleValidator $vehicleValidator
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $vehicleValidator->validate($data, true);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $customerId = $data['customer_id'];
        $customer = $entityManager->getRepository(Customer::class)->find($customerId);
        if (!$customer) {
            return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
        }

        $vehicle = $vehicleService->createOrUpdateVehicle($data, $customer);

        $entityManager->persist($vehicle);
        $entityManager->flush();

        return $this->json($vehicle, Response::HTTP_CREATED, [], ['groups' => ['vehicle_detail', 'customer_list']]);
    }

    #[Route('/{id}', name: 'app_vehicle_show', methods: ['GET'])]
    public function show(Vehicle $vehicle): Response
    {
        return $this->json($vehicle);
    }

    #[Route('/{id}/edit', name: 'app_vehicle_edit', methods: ['PUT', 'PATCH'])]
    public function edit(
        Request $request,
        Vehicle $vehicle,
        EntityManagerInterface $entityManager,
        VehicleService $vehicleService,
        VehicleValidator $vehicleValidator
    ): Response {
        $data = json_decode($request->getContent(), true);

        $errors = $vehicleValidator->validate($data, false);
        if (!empty($errors)) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $customer = null;
        if (isset($data['customer_id'])) {
            $customerId = $data['customer_id'];
            $customer = $entityManager->getRepository(Customer::class)->find($customerId);

            if (!$customer) {
                return $this->json(['error' => 'Customer not found'], Response::HTTP_NOT_FOUND);
            }

        }

        $vehicleService->createOrUpdateVehicle($data, $customer, $vehicle);

        $entityManager->flush();
        return $this->json($vehicle, 200, [], ['groups' => ['vehicle_detail', 'customer_list']]);
    }

    #[Route('/{id}/delete', name: 'app_vehicle_delete', methods: ['DELETE'])]
    public function delete(Vehicle $vehicle, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($vehicle);
        $entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
