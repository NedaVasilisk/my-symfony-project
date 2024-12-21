<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Appointment;
use App\Entity\Customer;
use App\Entity\Employee;
use App\Entity\Invoice;
use App\Entity\Notification;
use App\Entity\Part;
use App\Entity\Payment;
use App\Entity\PriceHistoryPart;
use App\Entity\PriceHistoryService;
use App\Entity\Repair;
use App\Entity\RepairAssignment;
use App\Entity\RepairItem;
use App\Entity\RepairPart;
use App\Entity\Service;
use App\Entity\User;
use App\Entity\Vehicle;
use App\Entity\Role;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Створення ролей
        $roles = ['Адміністратор', 'Менеджер сервісу', 'Механік', 'Консультант', 'Менеджер з продажу запчастин', 'Бухгалтер', 'Клієнт'];
        $roleEntities = [];
        foreach ($roles as $roleName) {
            $role = new Role();
            $role->setRoleName($roleName);
            $role->setDescription($faker->sentence());
            $manager->persist($role);
            $roleEntities[] = $role;
        }

        // Створення користувачів
        $users = [];
        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setUsername($faker->unique()->userName);
            $user->setPasswordHash(password_hash('password', PASSWORD_DEFAULT));
            $user->setFirstName($faker->firstName);
            $user->setLastName($faker->lastName);
            $user->setEmail($faker->unique()->email);
            $user->setIsActive(true);
            $user->setCreatedAt($faker->dateTimeThisYear());
            $manager->persist($user);
            $users[] = $user;
        }

        // Створення клієнтів
        $customers = [];
        foreach (array_slice($users, 0, 10) as $user) {
            $customer = new Customer();
            $customer->setUser($user);
            $customer->setFirstName($faker->firstName);
            $customer->setLastName($faker->lastName);
            $customer->setPhone($faker->phoneNumber);
            $customer->setEmail($faker->unique()->email);
            $customer->setAddress($faker->address);
            $manager->persist($customer);
            $customers[] = $customer;
        }

        // Створення співробітників
        $employees = [];
        foreach (array_slice($users, 10) as $user) {
            $employee = new Employee();
            $employee->setUser($user);
            $employee->setFirstName($user->getFirstName());
            $employee->setLastName($user->getLastName());
            $employee->setPosition($faker->jobTitle);
            $employee->setPhone($faker->phoneNumber);
            $employee->setEmail($user->getEmail());
            $employee->setSpecialization($faker->word);
            $manager->persist($employee);
            $employees[] = $employee;
        }

        // Створення транспортних засобів
        $vehicles = [];
        foreach ($customers as $customer) {
            for ($i = 0; $i < 2; $i++) {
                $vehicle = new Vehicle();
                $vehicle->setCustomer($customer);
                $vehicle->setVin($faker->unique()->regexify('[A-HJ-NPR-Z0-9]{17}'));
                $vehicle->setLicensePlate($faker->unique()->regexify('[A-Z]{2}[0-9]{4}[A-Z]{2}'));
                $vehicle->setMake($faker->company);
                $vehicle->setModel($faker->word);
                $vehicle->setYear($faker->year);
                $vehicle->setEngineType($faker->randomElement(['Бензин', 'Дизель', 'Електро', 'Гібрид', 'Інший']));
                $vehicle->setBatteryCapacity((string)$faker->randomFloat(2, 1.0, 100.0));
                $manager->persist($vehicle);
                $vehicles[] = $vehicle;
            }
        }

        // Створення послуг
        $services = [];
        for ($i = 0; $i < 10; $i++) {
            $service = new Service();
            $service->setName($faker->unique()->word);
            $service->setDescription($faker->sentence());
            $service->setCurrentPrice((string)$faker->randomFloat(2, 100, 1000));
            $manager->persist($service);
            $services[] = $service;
        }

        // Історія цін для послуг
        foreach ($services as $service) {
            $priceHistory = new PriceHistoryService();
            $priceHistory->setService($service);
            $priceHistory->setPrice($service->getCurrentPrice());
            $priceHistory->setEffectiveDate($faker->dateTimeThisYear());
            $manager->persist($priceHistory);
        }

       // Створення запчастин
        $parts = [];
        for ($i = 0; $i < 10; $i++) {
            $part = new Part();
            $part->setName($faker->unique()->word);
            $part->setManufacturer($faker->company);
            $part->setPartNumber($faker->unique()->numerify('PART###'));
            $part->setCurrentPrice((string)$faker->randomFloat(2, 50, 500));
            $part->setQuantityInStock($faker->numberBetween(1, 100));
            $manager->persist($part);
            $parts[] = $part;

            $priceHistoryPart = new PriceHistoryPart();
            $priceHistoryPart->setPart($part);
            $priceHistoryPart->setPrice($part->getCurrentPrice());
            $priceHistoryPart->setEffectiveDate($faker->dateTimeThisYear());
            $manager->persist($priceHistoryPart);
        }

//        // Створення ремонтів
//        $repairs = [];
//        foreach ($vehicles as $vehicle) {
//            $repair = new Repair();
//            $repair->setVehicle($vehicle);
//            $repair->setDateIn($faker->dateTimeThisYear());
//            $repair->setStatus($faker->randomElement(['Заплановано', 'В процесі', 'Завершено', 'Скасовано']));
//            $repair->setTotalCost((string)$faker->randomFloat(2, 500, 5000));
//            $manager->persist($repair);
//            $repairs[] = $repair;
//
//            $repairAssignment = new RepairAssignment();
//            $repairAssignment->setRepair($repair);
//            $repairAssignment->setEmployee($faker->randomElement($employees));
//            $manager->persist($repairAssignment);
//        }
//
//        // Створення елементів ремонту
//        foreach ($repairs as $repair) {
//            $repairItem = new RepairItem();
//            $repairItem->setRepair($repair);
//            $repairItem->setService($faker->randomElement($services));
//            $repairItem->setQuantity($faker->numberBetween(1, 5));
//            $repairItem->setPriceAtTime($repairItem->getService()->getCurrentPrice());
//            $manager->persist($repairItem);
//
//            $repairPart = new RepairPart();
//            $repairPart->setRepair($repair);
//            $repairPart->setPart($faker->randomElement($parts));
//            $repairPart->setQuantity($faker->numberBetween(1, 5));
//            $repairPart->setPriceAtTime($repairPart->getPart()->getCurrentPrice());
//            $manager->persist($repairPart);
//        }
//
//        // Створення повідомлень
//        foreach ($users as $user) {
//            $notification = new Notification();
//            $notification->setUser($user);
//            $notification->setMessage($faker->sentence());
//            $notification->setSentAt($faker->dateTimeThisYear());
//            $notification->setIsRead($faker->boolean);
//            $manager->persist($notification);
//        }
//
//        // Створення записів
//        foreach ($customers as $customer) {
//            $appointment = new Appointment();
//            $appointment->setCustomer($customer);
//            $appointment->setVehicle($faker->randomElement($vehicles));
//            $appointment->setScheduledDate($faker->dateTimeThisYear());
//            $appointment->setStatus($faker->randomElement(['Заплановано', 'Підтверджено', 'Скасовано', 'Завершено']));
//            $manager->persist($appointment);
//        }
//
//        // Створення рахунків та оплат
//        foreach ($repairs as $repair) {
//            $invoice = new Invoice();
//            $invoice->setRepair($repair);
//            $invoice->setDateIssued($faker->dateTimeThisYear());
//            $invoice->setTotalAmount($repair->getTotalCost());
//            $invoice->setIsPaid($faker->boolean);
//            $manager->persist($invoice);
//
//            if ($invoice->isPaid()) {
//                $payment = new Payment();
//                $payment->setInvoice($invoice);
//                $payment->setPaymentDate($faker->dateTimeThisYear());
//                $payment->setAmount($invoice->getTotalAmount());
//                $payment->setPaymentMethod($faker->randomElement(['Готівка', 'Картка', 'Онлайн', 'Банківський переказ']));
//                $manager->persist($payment);
//            }
//        }

        $manager->flush();
    }
}
