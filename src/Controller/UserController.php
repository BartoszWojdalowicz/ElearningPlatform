<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Entity\User;
use App\Repository\UserRepository;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints;
use Webmozart\Assert\Assert;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractFOSRestController
{

    /**
     * @Rest\Post("/register", name="app_register")
     * @Rest\RequestParam(name="password")
     * @Rest\RequestParam(name="email",requirements=@Constraints\Email)
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ParamFetcherInterface $fetcher
     * @param SerializerInterface $serializer
     * @param UserRepository $userRepository
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function register( UserPasswordEncoderInterface $passwordEncoder,ParamFetcherInterface $fetcher,SerializerInterface $serializer, UserRepository $userRepository
    ): JsonResponse
    {

        $data=$fetcher->all();
        $password = $data['password'];
        $email = $data['email'];
        Assert::stringNotEmpty($password, 'Password can not be empty!!!'); //500
        Assert::email($email,'invalid Email'); //500
        if($userRepository->findOneBy(['email'=>$email])){
            return new JsonResponse('email exists',409);
        }

        $user = $serializer->denormalize($data,User::class);
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $password
            ))
            ->setProfile(new Profile());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse(['id'=>$user->getId(),'email'=>$user->getEmail()],201);
    }
}
