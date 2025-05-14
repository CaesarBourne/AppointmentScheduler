<?php

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $startTime = null;

    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $endTime = null;

    #[ORM\ManyToOne(targetEntity: Participant::class, inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Participant $participant = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartTime(): ?DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(?Participant $participant): static
    {
        $this->participant = $participant;

        return $this;
    }
}

// namespace App\Entity;

// use App\Repository\AppointmentRepository;
// use Doctrine\ORM\Mapping as ORM;

// #[ORM\Entity(repositoryClass: AppointmentRepository::class)]
// class Appointment
// {
//     #[ORM\Id]
//     #[ORM\GeneratedValue]
//     #[ORM\Column]
//     private ?int $id = null;

//     #[ORM\Column]
//     private ?\DateTime $startTime = null;

//     #[ORM\Column]
//     private ?\DateTime $endTime = null;

//     #[ORM\ManyToOne(targetEntity: Participant::class)]
//     #[ORM\JoinColumn(nullable: false)]
//     private ?Participant $participant = null;

//     public function getId(): ?int
//     {
//         return $this->id;
//     }

//     public function getStartTime(): ?\DateTime
//     {
//         return $this->startTime;
//     }

//     public function setStartTime(\DateTime $startTime): static
//     {
//         $this->startTime = $startTime;

//         return $this;
//     }

//     public function getEndTime(): ?\DateTime
//     {
//         return $this->endTime;
//     }

//     public function setEndTime(\DateTime $endTime): static
//     {
//         $this->endTime = $endTime;

//         return $this;
//     }

//     public function getParticipant(): ?Participant
//     {
//         return $this->participant;
//     }

//     public function setParticipant(Participant $participant): static
//     {
//         $this->participant = $participant;

//         return $this;
//     }
// }
