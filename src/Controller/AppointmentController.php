<?php

namespace App\Controller;

use App\Service\AppointmentService;
use App\Repository\AppointmentRepository;
use App\Entity\Appointment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/appointments')]
class AppointmentController extends AbstractController
{
    private AppointmentService $appointmentService;
    private AppointmentRepository $appointmentRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        AppointmentService $appointmentService,
        AppointmentRepository $appointmentRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->appointmentService = $appointmentService;
        $this->appointmentRepository = $appointmentRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (
            !isset($data['participantId'], $data['startTime'], $data['endTime']) ||
            empty($data['participantId']) || empty($data['startTime']) || empty($data['endTime'])
        ) {
            return new JsonResponse(['status' => 'error', 'message' => 'Missing required fields.'], 400);
        }

        try {
            $appointment = $this->appointmentService->createAppointment(
                $data['participantId'],
                new \DateTime($data['startTime']),
                new \DateTime($data['endTime'])
            );

            return new JsonResponse([
                'status' => 'success',
                'appointment' => [
                    'id' => $appointment->getId(),
                    'participant' => $appointment->getParticipant()->getName(),
                    'startTime' => $appointment->getStartTime()->format(DATE_ATOM),
                    'endTime' => $appointment->getEndTime()->format(DATE_ATOM),
                ]
            ], 201);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    #[Route('/{id}', methods: ['GET'])]
    public function viewAppointment(int $id): JsonResponse
    {
        $appointment = $this->appointmentRepository->find($id);

        if (!$appointment) {
            return new JsonResponse(['status' => 'error', 'message' => 'Appointment not found.'], 404);
        }

        return new JsonResponse([
            'id' => $appointment->getId(),
            'participant' => $appointment->getParticipant()->getName(),
            'startTime' => $appointment->getStartTime()->format(DATE_ATOM),
            'endTime' => $appointment->getEndTime()->format(DATE_ATOM),
        ]);
    }

    #[Route('', methods: ['GET'])]
    public function listAppointments(): JsonResponse
    {
        $appointments = $this->appointmentRepository->findAll();

        $data = array_map(function (Appointment $a) {
            return [
                'id' => $a->getId(),
                'participant' => $a->getParticipant()->getName(),
                'startTime' => $a->getStartTime()->format(DATE_ATOM),
                'endTime' => $a->getEndTime()->format(DATE_ATOM),
            ];
        }, $appointments);

        return new JsonResponse($data);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteAppointment(int $id): JsonResponse
    {
        $appointment = $this->appointmentRepository->find($id);

        if (!$appointment) {
            return new JsonResponse(['status' => 'error', 'message' => 'Appointment not found.'], 404);
        }

        $this->entityManager->remove($appointment);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Appointment deleted.']);
    }
}
