<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VideoRepository::class)
 */
class Video
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $duration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $durationString;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $extension;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $resolutionX;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $resolutionY;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $frameRate;

    /**
     * @ORM\OneToOne(targetEntity=Lesson::class, inversedBy="video", cascade={"persist", "remove"})
     */
    private $lesson;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDuration(): ?float
    {
        return $this->duration;
    }

    public function setDuration(?float $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getLesson(): ?Lesson
    {
        return $this->lesson;
    }

    public function setLesson(?Lesson $lesson): self
    {
        $this->lesson = $lesson;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(?string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getResolutionX(): ?int
    {
        return $this->resolutionX;
    }

    public function setResolutionX(?int $resolutionX): self
    {
        $this->resolutionX = $resolutionX;

        return $this;
    }

    public function getResolutionY(): ?int
    {
        return $this->resolutionY;
    }

    public function setResolutionY(?int $resolutionY): self
    {
        $this->resolutionY = $resolutionY;

        return $this;
    }

    public function getFrameRate(): ?float
    {
        return $this->frameRate;
    }

    public function setFrameRate(?float $frameRate): self
    {
        $this->frameRate = $frameRate;

        return $this;
    }

    public function getDurationString(): ?string
    {
        return $this->durationString;
    }

    public function setDurationString(?string $durationString): self
    {
        $this->durationString = $durationString;

        return $this;
    }
}
