<?php

namespace App\Controller;

// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Component\Routing\Attribute\Route;

// final class AppointmentController extends AbstractController
// {
//     #[Route('/appointment', name: 'app_appointment')]
//     public function index(): JsonResponse
//     {
//         return $this->json([
//             'message' => 'Welcome to your new controller!',
//             'path' => 'src/Controller/AppointmentController.php',
//         ]);
//     }
// }

// src/Controller/AppointmentController.php
namespace App\Controller;

use App\Service\AppointmentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AppointmentController extends AbstractController
{
    private $appointmentService;

    public function __construct(AppointmentService $appointmentService)
    {
        $this->appointmentService = $appointmentService;
    }

    /**
     * @Route("/appointments", methods={"POST"})
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
}