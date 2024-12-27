<?php

namespace App\EventListener;

use App\Entity\Appointment;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;

class AppointmentRescheduleListener
{

    public function postUpdate(Appointment $appointment, LifecycleEventArgs $args): void
    {
        if ($appointment->getStatus() === 'Rescheduled') {

        }
    }
}