<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookingRepository::class)
 */
class Booking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $beginAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;


    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $all_day;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $background_color;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $border_color;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $text_color;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="bookings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;






    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBeginAt(): ?\DateTimeInterface
    {
        return $this->beginAt;
    }

    public function setBeginAt(?\DateTimeInterface $beginAt): self
    {
        $this->beginAt = $beginAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAllDay(): ?bool
    {
        return $this->all_day;
    }

    public function setAllDay(bool $all_day): self
    {
        $this->all_day = $all_day;

        return $this;
    }

    public function getBackgroundColor(): ?string
    {
        return $this->background_color;
    }

    public function setBackgroundColor(string $background_color): self
    {
        $this->background_color = $background_color;

        return $this;
    }

    public function getBorderColor(): ?string
    {
        return $this->border_color;
    }

    public function setBorderColor(string $border_color): self
    {
        $this->border_color = $border_color;

        return $this;
    }

    public function getTextColor(): ?string
    {
        return $this->text_color;
    }

    public function setTextColor(string $text_color): self
    {
        $this->text_color = $text_color;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
