<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LessonRepository::class)
 */
class Lesson
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isFree;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $inOrder;

    /**
     * @ORM\ManyToOne(targetEntity=Section::class, inversedBy="lesson")
     * @ORM\JoinColumn(nullable=false)
     */
    private $section;

    /**
     * @ORM\OneToOne(targetEntity=Video::class, mappedBy="lesson", cascade={"persist", "remove"})
     */
    private $video;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(?int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getIsFree(): ?bool
    {
        return $this->isFree;
    }

    public function setIsFree(?bool $isFree): self
    {
        $this->isFree = $isFree;

        return $this;
    }

    public function getInOrder(): ?int
    {
        return $this->inOrder;
    }

    public function setInOrder(?int $inOrder): self
    {
        $this->inOrder = $inOrder;

        return $this;
    }

    public function getSection(): ?Section
    {
        return $this->section;
    }

    public function setSection(?Section $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getVideo(): ?Video
    {
        return $this->video;
    }

    public function setVideo(?Video $video): self
    {
        // unset the owning side of the relation if necessary
        if ($video === null && $this->video !== null) {
            $this->video->setLesson(null);
        }

        // set the owning side of the relation if necessary
        if ($video !== null && $video->getLesson() !== $this) {
            $video->setLesson($this);
        }

        $this->video = $video;

        return $this;
    }
}
