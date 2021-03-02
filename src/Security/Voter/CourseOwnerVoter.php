<?php

namespace App\Security\Voter;

use App\Repository\UserCourseRepository;
use App\Entity\Course;
use App\Entity\User;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class CourseOwnerVoter
 * @package App\Security\Voter
 */
class CourseOwnerVoter extends Voter
{
    /**
     * @var UserCourseRepository
     */
    private UserCourseRepository $repository;


    /**
     * CourseOwnerVoter constructor.
     * @param UserCourseRepository $repository
     */
    public function __construct(UserCourseRepository $repository)
    {
        $this->repository=$repository;
    }

    #[Pure]
    protected function supports($attribute, $subject): bool
    {
        return in_array($attribute, ['COURSE_OWNER', 'COURSE_BUYER'])
            && $subject instanceof Course;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }
        /** @var Course $course */
        $course=$subject;

        return match($attribute){
            'COURSE_OWNER'=> $this->isOwner($user,$course),
            'COURSE_BUYER'=> $this->isBuyer($user,$course),
        };
    }

    /**
     * @param User $user
     * @param Course $course
     * @return bool
     */
    private function isOwner(User $user,Course $course): bool
    {
        $isOwner=$this->repository->findOneBy(['user'=>$user,'course'=>$course,'IsOwner'=>true]);
        return !empty($isOwner);
    }

    /**
     * @param User $user
     * @param Course $course
     * @return bool
     */
    private function isBuyer(User $user,Course $course): bool
    {
        $isBuyer=$this->repository->findOneBy(['user'=>$user,'course'=>$course,'isBuyer'=>true]);
        return !empty($isBuyer);
    }
}
