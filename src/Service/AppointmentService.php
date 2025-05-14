<?php

namespace App\Service;

use App\Entity\Appointment;
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

    public function createAppointment(string $email, \DateTime $startTime, \DateTime $endTime): Appointment
    {
        $participant = $this->participantRepository->findOneBy(['email' => $email]);

        if (!$participant) {
            // Option 1: Throw an error if participant doesn't exist
            throw new \Exception('Participant with this email not found.');
            
            // Option 2: Or create a new participant if that's your business logic
            // $participant = new Participant();
            // $participant->setEmail($email);
            // $this->entityManager->persist($participant);
            // $this->entityManager->flush();
        }

        $now = new \DateTime();

        // Ensure start and end times are not in the past
        if ($startTime < $now || $endTime < $now) {
            throw new \Exception('Appointment times must be in the future.');
        }

        // Ensure start time is before end time
        if ($startTime >= $endTime) {
            throw new \Exception('Start time must be before end time.');
        }

        // Check for overlapping appointments
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('a')
            ->from(Appointment::class, 'a')
            ->where('a.participant = :participant')
            ->andWhere('a.startTime < :endTime')
            ->andWhere('a.endTime > :startTime')
            ->setParameter('participant', $participant)
            ->setParameter('startTime', $startTime)
            ->setParameter('endTime', $endTime);

        $conflictingAppointments = $qb->getQuery()->getResult();

        if (count($conflictingAppointments) > 0) {
            throw new \Exception('Participant already has an appointment scheduled during this time.');
        }

        $appointment = new Appointment();
        $appointment->setParticipant($participant);
        $appointment->setStartTime($startTime);
        $appointment->setEndTime($endTime);

        $this->entityManager->persist($appointment);
        $this->entityManager->flush();

        return $appointment;
    }
}



// namespace App\Service;

// use App\Entity\Appointment;
// use App\Entity\Participant;
// use App\Repository\ParticipantRepository;
// use Doctrine\ORM\EntityManagerInterface;

// class AppointmentService
// {
//     private EntityManagerInterface $entityManager;
//     private ParticipantRepository $participantRepository;

//     public function __construct(
//         EntityManagerInterface $entityManager,
//         ParticipantRepository $participantRepository
//     ) {
//         $this->entityManager = $entityManager;
//         $this->participantRepository = $participantRepository;
//     }

//     public function createAppointment(int $participantId, \DateTime $startTime, \DateTime $endTime): Appointment
//     {
//         $participant = $this->participantRepository->find($participantId);

//         if (!$participant) {
//             throw new \Exception('Participant not found.');
//         }

//         $now = new \DateTime();

//         // Ensure start and end times are not in the past
//         if ($startTime < $now || $endTime < $now) {
//             throw new \Exception('Appointment times must be in the future.');
//         }

//         // Ensure start time is before end time
//         if ($startTime >= $endTime) {
//             throw new \Exception('Start time must be before end time.');
//         }

//         // Check for overlapping appointments
//         $qb = $this->entityManager->createQueryBuilder();
//         $qb->select('a')
//             ->from(Appointment::class, 'a')
//             ->where('a.participant = :participant')
//             ->andWhere('a.startTime < :endTime')
//             ->andWhere('a.endTime > :startTime')
//             ->setParameter('participant', $participant)
//             ->setParameter('startTime', $startTime)
//             ->setParameter('endTime', $endTime);

//         $conflictingAppointments = $qb->getQuery()->getResult();

//         if (count($conflictingAppointments) > 0) {
//             throw new \Exception('Participant already has an appointment scheduled during this time.');
//         }

//         $appointment = new Appointment();
//         $appointment->setParticipant($participant);
//         $appointment->setStartTime($startTime);
//         $appointment->setEndTime($endTime);

//         $this->entityManager->persist($appointment);
//         $this->entityManager->flush();

//         return $appointment;
//     }
// }


