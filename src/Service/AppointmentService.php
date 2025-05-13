<?php
namespace App\Service;



use App\Repository\AppointmentRepository;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;

class AppointmentService
{
    private $appointmentRepository;
    private $participantRepository;
    private $em;

    public function __construct(AppointmentRepository $appointmentRepository, ParticipantRepository $participantRepository, EntityManagerInterface $em)
    {
        $this->appointmentRepository = $appointmentRepository;
        $this->participantRepository = $participantRepository;
        $this->em = $em;
    }

    public function createAppointment($participantId, \DateTime $startTime, \DateTime $endTime)
    {
        $participant = $this->participantRepository->find($participantId);
        if (!$participant) {
            throw new \Exception('Participant not found.');
        }

        // Check if the appointment overlaps for the same participant
        $existingAppointments = $this->appointmentRepository->findBy([
            'participant' => $participant
        ]);

        foreach ($existingAppointments as $appointment) {
            if (($startTime < $appointment->getEndTime()) && ($endTime > $appointment->getStartTime())) {
                throw new \Exception('This appointment overlaps with another.');
            }
        }

        $appointment = new Appointment($startTime, $endTime, $participant);
        $this->em->persist($appointment);
        $this->em->flush();

        return $appointment;
    }
}