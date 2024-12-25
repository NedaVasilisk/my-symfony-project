<?php

namespace App\Service;

use App\Entity\Appointment;
use App\Entity\Customer;
use App\Entity\Vehicle;

class AppointmentService
{
    public function createOrUpdateAppointment(array $data, Customer $customer, Vehicle $vehicle, ?Appointment $appointment = null): Appointment
    {
        if (!$appointment) {
            $appointment = new Appointment();
        }

        $appointment->setCustomer($customer);
        $appointment->setVehicle($vehicle);
        $appointment->setScheduledDate(isset($data['scheduledDate']) ? new \DateTime($data['scheduledDate']) : new \DateTime());
        $appointment->setStatus($data['status'] ?? 'Pending');

        return $appointment;
    }
}
