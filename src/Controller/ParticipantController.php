<?php

// src/Controller/ParticipantController.php
namespace App\Controller;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class ParticipantController extends AbstractController
{
    private $participantRepository;

    public function __construct(ParticipantRepository $participantRepository)
    {
        $this->participantRepository = $participantRepository;
    }

    /**
     * @Route("/api/participants", methods={"POST"})
     */
    public function create(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        
        $participant = new Participant($data['name']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($participant);
        $em->flush();

        return new JsonResponse(['status' => 'success', 'participant' => $participant], 200);
    }

    /**
     * @Route("/api/participants/{id}", methods={"GET"})
     */
    public function viewParticipant(int $id)
    {
        $participant = $this->participantRepository->find($id);

        if (!$participant) {
            return new JsonResponse(['status' => 'error', 'message' => 'Participant not found.'], 404);
        }

        return new JsonResponse($participant, 200);
    }

    /**
     * @Route("/api/participants", methods={"GET"})
     */
    public function listParticipants()
    {
        $participants = $this->participantRepository->findAll();
        return new JsonResponse($participants, 200);
    }

    /**
     * @Route("/api/participants/{id}", methods={"DELETE"})
     */
    public function deleteParticipant(int $id)
    {
        $participant = $this->participantRepository->find($id);

        if (!$participant) {
            return new JsonResponse(['status' => 'error', 'message' => 'Participant not found.'], 404);
        }

        $this->getDoctrine()->getManager()->remove($participant);
        $this->getDoctrine()->getManager()->flush();

        return new JsonResponse(['status' => 'success', 'message' => 'Participant deleted.'], 200);
    }
}
