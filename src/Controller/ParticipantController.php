<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/participants')]
class ParticipantController extends AbstractController
{
    private ParticipantRepository $participantRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        ParticipantRepository $participantRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->participantRepository = $participantRepository;
        $this->entityManager = $entityManager;
    }

    // #[Route('', methods: ['POST'])]
    // public function create(Request $request): JsonResponse
    // {
    //     $data = json_decode($request->getContent(), true);

    //     if (!isset($data['name']) || empty(trim($data['name']))) {
    //         return new JsonResponse(['status' => 'error', 'message' => 'Name is required.'], 400);
    //     }

    //     $participant = new Participant($data['name']);

    //     $this->entityManager->persist($participant);
    //     $this->entityManager->flush();

    //     return new JsonResponse([
    //         'status' => 'success',
    //         'participant' => ['id' => $participant->getId(), 'name' => $participant->getName()]
    //     ], 201);
    // }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['name']) || empty(trim($data['name']))) {
            return new JsonResponse(['status' => 'error', 'message' => 'The "name" field is required.'], 400);
        }

        $participant = new Participant();
        $participant->setName(trim($data['name']));

        $this->entityManager->persist($participant);
        $this->entityManager->flush();

        return new JsonResponse([
            'status' => 'success',
            'participant' => [
                'id' => $participant->getId(),
                'name' => $participant->getName()
            ]
        ], 201);
    }

    #[Route('/{id}', methods: ['GET'])]
    public function viewParticipant(int $id): JsonResponse
    {
        $participant = $this->participantRepository->find($id);

        if (!$participant) {
            return new JsonResponse(['status' => 'error', 'message' => 'Participant not found.'], 404);
        }

        return new JsonResponse([
            'id' => $participant->getId(),
            'name' => $participant->getName()
        ]);
    }

    #[Route('', methods: ['GET'])]
    public function listParticipants(): JsonResponse
    {
        $participants = $this->participantRepository->findAll();

        $data = array_map(function (Participant $p) {
            return ['id' => $p->getId(), 'name' => $p->getName()];
        }, $participants);

        return new JsonResponse($data);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function deleteParticipant(int $id): JsonResponse
    {
        $participant = $this->participantRepository->find($id);

        if (!$participant) {
            return new JsonResponse(['status' => 'error', 'message' => 'Participant not found.'], 404);
        }

        $this->entityManager->remove($participant);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Participant deleted.']);
    }
}
