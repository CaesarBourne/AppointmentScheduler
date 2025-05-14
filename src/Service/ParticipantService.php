<?php

namespace App\Service;

use App\Entity\Participant;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;

class ParticipantService
{
    public function __construct(
        private ParticipantRepository $participantRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function createParticipant(string $name, string $email): Participant
    {

        $existing = $this->participantRepository->findOneBy(['email' => $email]);

        if ($existing) {
            return $existing; // Reuse existing participant
        }



        $participant = new Participant();
        $participant->setName(trim($name));
        $participant->setEmail(trim($email));

        $this->entityManager->persist($participant);
        $this->entityManager->flush();

        return $participant;
    }

    public function getParticipant(int $id): ?Participant
    {
        return $this->participantRepository->find($id);
    }

    public function listParticipants(): array
    {
        return $this->participantRepository->findAll();
    }

    public function deleteParticipant(Participant $participant): void
    {
        $this->entityManager->remove($participant);
        $this->entityManager->flush();
    }

    public function getAppointments(Participant $participant): array
    {
        return $participant->getAppointments()->toArray();
    }

    public function findOneByEmail(string $email): ?Participant
{
    return $this->findOneBy(['email' => $email]);
}

}
