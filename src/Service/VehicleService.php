<?php

namespace App\Service;

use App\Entity\Customer;
use App\Entity\Vehicle;
use DateTime;

class VehicleService
{
    public function createOrUpdateVehicle(array $data, Customer $customer, ?Vehicle $vehicle = null): Vehicle
    {
        if (!$vehicle) {
            $vehicle = new Vehicle();
            $vehicle->setCustomer($customer);
        } else {
            if ($customer) {
                $vehicle->setCustomer($customer);
            }
        }

        $vehicle->setVin($data['vin'] ?? null);
        $vehicle->setLicensePlate($data['licensePlate'] ?? null);
        $vehicle->setMake($data['make'] ?? null);
        $vehicle->setModel($data['model'] ?? null);
        $vehicle->setYear(isset($data['year']) ? (int)$data['year'] : null);
        $vehicle->setEngineType($data['engineType'] ?? null);
        $vehicle->setBatteryCapacity($data['batteryCapacity'] ?? null);

        if (isset($data['lastIotUpdate'])) {
            $vehicle->setLastIotUpdate(new DateTime($data['lastIotUpdate']));
        }

        return $vehicle;
    }
}
