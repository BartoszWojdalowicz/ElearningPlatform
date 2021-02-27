<?php

namespace App\Entity;

use App\Repository\UserCourseRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserCourseRepository::class)
 */
class UserCourse
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="userCourses")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Course::class, inversedBy="userCourses")
     */
    private $course;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $IsOwner;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isBuyer;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $userRating;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;

        return $this;
    }

    public function getIsOwner(): ?bool
    {
        return $this->IsOwner;
    }

    public function setIsOwner(?bool $IsOwner): self
    {
        $this->IsOwner = $IsOwner;

        return $this;
    }

    public function getIsBuyer(): ?bool
    {
        return $this->isBuyer;
    }

    public function setIsBuyer(?bool $isBuyer): self
    {
        $this->isBuyer = $isBuyer;

        return $this;
    }

    public function getUserRating(): ?float
    {
        return $this->userRating;
    }

    public function setUserRating(?float $userRating): self
    {
        $this->userRating = $userRating;

        return $this;
    }
}
