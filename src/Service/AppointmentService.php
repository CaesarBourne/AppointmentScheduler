<?php

// }

// src/Service/AppointmentService.php
// namespace App\Service;
// use App\Entity\Appointment;

// use App\Repository\AppointmentRepository;
// use App\Repository\ParticipantRepository;
// use Doctrine\ORM\EntityManagerInterface;

// class AppointmentService
// {
//     private $appointmentRepository;
//     private $participantRepository;
//     private $em;

//     public function __construct(AppointmentRepository $appointmentRepository, ParticipantRepository $participantRepository, EntityManagerInterface $em)
//     {
//         $this->appointmentRepository = $appointmentRepository;
//         $this->participantRepository = $participantRepository;
//         $this->em = $em;
//     }

//     public function createAppointment($participantId, \DateTime $startTime, \DateTime $endTime)
//     {
//         $participant = $this->participantRepository->find($participantId);
//         if (!$participant) {
//             throw new \Exception('Participant not found.');
//         }

//         // Check if the appointment overlaps for the same participant
//         $existingAppointments = $this->appointmentRepository->findBy([
//             'participant' => $participant
//         ]);

//         foreach ($existingAppointments as $appointment) {
//             if (($startTime < $appointment->getEndTime()) && ($endTime > $appointment->getStartTime())) {
//                 throw new \Exception('This appointment overlaps with another.');
//             }
//         }

//         $appointment = new Appointment();
//         $appointment->setStartTime($startTime);
//         $appointment->setEndTime($endTime);
//         $appointment->setParticipant($participant);

//         $this->em->persist($appointment);
//         $this->em->flush();

//         return $appointment;
//     }
// }


namespace App\Service;

use App\Entity\Appointment; // ✅ THIS IS REQUIRED
use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;

class AppointmentService
{
    private EntityManagerInterface $entityManager;
    private ParticipantRepository $participantRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository
    ) {
        $this->entityManager = $entityManager;
        $this->participantRepository = $participantRepository;
    }

    public function createAppointment(int $participantId, \DateTime $startTime, \DateTime $endTime): Appointment
    {
        $participant = $this->participantRepository->find($participantId);

        if (!$participant) {
            throw new \Exception('Participant not found.');
        }

        $appointment = new Appointment(); // 💥 This line fails if Appointment is not imported
        $appointment->setParticipant($participant);
        $appointment->setStartTime($startTime);
        $appointment->setEndTime($endTime);

        $this->entityManager->persist($appointment);
        $this->entityManager->flush();

        return $appointment;
    }
}
