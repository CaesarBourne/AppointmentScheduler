<?php

namespace App\Controller;

use App\Service\ParticipantService;
use App\Entity\Participant;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/participants')]
class ParticipantController extends AbstractController
{
    public function __construct(
        private ParticipantService $participantService
    ) {}

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            if (!isset($data['name']) || empty(trim($data['name']))) {
                return new JsonResponse(['status' => 'error', 'message' => 'The "name" field is required.'], 400);
            }

            $participant = $this->participantService->createParticipant($data['name']);

            return new JsonResponse([
                'status' => 'success',
                'participant' => [
                    'id' => $participant->getId(),
                    'name' => $participant->getName()
                ]
            ], 201);
        } catch (\Throwable $e) {
            return new JsonResponse(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    #[Route('/{id}', methods: ['GET'])]
    public function viewParticipant(int $id): JsonResponse
    {
        try {
            $participant = $this->participantService->getParticipant($id);
            if (!$participant) {
                return new JsonResponse(['status' => 'error', 'message' => 'Participant not found.'], 404);
            }

            return new JsonResponse([
                'id' => $participant->getId(),
                'name' => $participant->getName()
            ]);
        } catch (\Throwable $e) {
            return new JsonResponse(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    #[Route('', methods: ['GET'])]
    public function listParticipants(): JsonResponse
    {
        try {
            $participants = $this->participantService->listParticipants();

            $data = array_map(fn(Participant $p) => [
                'id' => $p->getId(),
                'name' => $p->getName()
            ], $participants);

            return new JsonResponse($data);
        } catch (\Throwable $e) {
            return new JsonResponse(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteParticipant(int $id): JsonResponse
    {
        try {
            $participant = $this->participantService->getParticipant($id);

            if (!$participant) {
                return new JsonResponse(['status' => 'error', 'message' => 'Participant not found.'], 404);
            }

            $this->participantService->deleteParticipant($participant);

            return new JsonResponse(['status' => 'success', 'message' => 'Participant deleted.']);
        } catch (\Throwable $e) {
            return new JsonResponse(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    #[Route('/{id}/appointments', methods: ['GET'])]
    public function getAppointmentsForParticipant(int $id): JsonResponse
    {
        try {
            $participant = $this->participantService->getParticipant($id);

            if (!$participant) {
                return new JsonResponse(['status' => 'error', 'message' => 'Participant not found.'], 404);
            }

            $appointments = $this->participantService->getAppointments($participant);

            $data = array_map(fn($a) => [
                'id' => $a->getId(),
                'startTime' => $a->getStartTime()->format(DATE_ATOM),
                'endTime' => $a->getEndTime()->format(DATE_ATOM),
            ], $appointments);

            return new JsonResponse(['status' => 'success', 'appointments' => $data]);
        } catch (\Throwable $e) {
            return new JsonResponse(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }
}


// namespace App\Controller;

// use App\Entity\Participant;
// use App\Repository\ParticipantRepository;
// use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\Routing\Annotation\Route;

// #[Route('/api/participants')]
// class ParticipantController extends AbstractController
// {
//     private ParticipantRepository $participantRepository;
//     private EntityManagerInterface $entityManager;

//     public function __construct(
//         ParticipantRepository $participantRepository,
//         EntityManagerInterface $entityManager
//     ) {
//         $this->participantRepository = $participantRepository;
//         $this->entityManager = $entityManager;
//     }

//     #[Route('', methods: ['POST'])]
//     public function create(Request $request): JsonResponse
//     {
//         $data = json_decode($request->getContent(), true);

//         if (!isset($data['name']) || empty(trim($data['name']))) {
//             return new JsonResponse(['status' => 'error', 'message' => 'The "name" field is required.'], 400);
//         }

//         $participant = new Participant();
//         $participant->setName(trim($data['name']));

//         $this->entityManager->persist($participant);
//         $this->entityManager->flush();

//         return new JsonResponse([
//             'status' => 'success',
//             'participant' => [
//                 'id' => $participant->getId(),
//                 'name' => $participant->getName()
//             ]
//         ], 201);
//     }

//     #[Route('/{id}', methods: ['GET'])]
//     public function viewParticipant(int $id): JsonResponse
//     {
//         $participant = $this->participantRepository->find($id);

//         if (!$participant) {
//             return new JsonResponse(['status' => 'error', 'message' => 'Participant not found.'], 404);
//         }

//         return new JsonResponse([
//             'id' => $participant->getId(),
//             'name' => $participant->getName()
//         ]);
//     }

//     #[Route('', methods: ['GET'])]
//     public function listParticipants(): JsonResponse
//     {
//         $participants = $this->participantRepository->findAll();

//         $data = array_map(function (Participant $p) {
//             return ['id' => $p->getId(), 'name' => $p->getName()];
//         }, $participants);

//         return new JsonResponse($data);
//     }

//     #[Route('/{id}', methods: ['DELETE'])]
//     public function deleteParticipant(int $id): JsonResponse
//     {
//         $participant = $this->participantRepository->find($id);

//         if (!$participant) {
//             return new JsonResponse(['status' => 'error', 'message' => 'Participant not found.'], 404);
//         }

//         $this->entityManager->remove($participant);
//         $this->entityManager->flush();

//         return new JsonResponse(['status' => 'success', 'message' => 'Participant deleted.']);
//     }

//     #[Route('/{id}/appointments', methods: ['GET'])]
// public function getAppointmentsForParticipant(int $id): JsonResponse
// {
//     $participant = $this->participantRepository->find($id);

//     if (!$participant) {
//         return new JsonResponse(['status' => 'error', 'message' => 'Participant not found.'], 404);
//     }

//     $appointments = $participant->getAppointments();

//     $data = [];

//     foreach ($appointments as $appointment) {
//         $data[] = [
//             'id' => $appointment->getId(),
//             'startTime' => $appointment->getStartTime()->format(DATE_ATOM),
//             'endTime' => $appointment->getEndTime()->format(DATE_ATOM),
//         ];
//     }

//     return new JsonResponse([
//         'status' => 'success',
//         'appointments' => $data
//     ]);
// }
// }
