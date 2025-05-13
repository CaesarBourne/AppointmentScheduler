<?php

namespace App\Controller;


use App\Service\AppointmentService;
use App\Repository\AppointmentRepository;
use App\Entity\Appointment;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AppointmentController extends AbstractController
{
    private $appointmentService;
    private $appointmentRepository;

    public function __construct(AppointmentService $appointmentService, AppointmentRepository $appointmentRepository)
    {
        $this->appointmentService = $appointmentService;
        $this->appointmentRepository = $appointmentRepository;
    }

    /**
     * @Route("/api/appointments", methods={"POST"})
     */
    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        try {
            $appointment = $this->appointmentService->createAppointment(
                $data['participantId'],
                new \DateTime($data['startTime']),
                new \DateTime($data['endTime'])
            );

            return new JsonResponse(['status' => 'success', 'appointment' => $appointment], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * @Route("/api/appointments/{id}", methods={"GET"})
     */
    public function viewAppointment(int $id)
    {
        $appointment = $this->appointmentRepository->find($id);

        if (!$appointment) {
            return new JsonResponse(['status' => 'error', 'message' => 'Appointment not found.'], 404);
        }

        return new JsonResponse($appointment, 200);
    }

    /**
     * @Route("/api/appointments", methods={"GET"})
     */
    public function listAppointments()
    {
        $appointments = $this->appointmentRepository->findAll();
        return new JsonResponse($appointments, 200);
    }

    /**
     * @Route("/api/appointments/{id}", methods={"DELETE"})
     */
    public function deleteAppointment(int $id)
    {
        $appointment = $this->appointmentRepository->find($id);

        if (!$appointment) {
            return new JsonResponse(['status' => 'error', 'message' => 'Appointment not found.'], 404);
        }

        $this->getDoctrine()->getManager()->remove($appointment);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Appointment deleted.'], 200);
    }
}