<?php

namespace App\Controller;

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
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use Webmozart\Assert\Assert;

/**
 * Class UserController
 * @package App\Controller
 */
class UserController extends AbstractFOSRestController
{

    /**
     * @Rest\Post("/register", name="app_register")
     * @Rest\QueryParam(name="password")
     * @Rest\QueryParam(name="email",requirements=@Constraints\Email)
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ParamFetcherInterface $fetcher
     * @param SerializerInterface $serializer
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

        if(!$password ||!$email){return new JsonResponse('dupa',404);}

        $user = $serializer->denormalize($data,User::class);
        $user->setPassword(
            $passwordEncoder->encodePassword(
                $user,
                $password
            )
        );

        $validator = new EmailValidator();
        if (!$validator->isValid($email, new RFCValidation())) {
            return new JsonResponse('invalid Email Address',400);
        }
        $userExist=$userRepository->findOneBy(['email'=> $email]);
        if($userExist){return new JsonResponse('userExist',409);}

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

//        $url= new GeneratedUrl();
//        $url->setExpiredAt(2);
//        $url->setHash(40);
//        $url->setType(1);
//        $url->setUser($user);
//
//        $entityManager->persist($url);
//        $entityManager->flush();
//
//        $confirmEmail=new TemplatedEmail();
//        $confirmEmail
//            ->from(new Address('wojdalowicz@op.pl', 'MyEndomondo'))
//            ->to($user->getEmail())
//            ->subject('Please Confirm your Email')
//            ->htmlTemplate('registration/confirmation_email.html.twig')
//            ->context([
//                'url' => '/verify/email/'.$url->getHash(),
//                'user' => $user->getUsername(),
//                'expiredAt'=> $url->getExpiredAt(),
//            ]);
//        $mailer->send($confirmEmail);

        return new JsonResponse(['id'=>$user->getId(),'email'=>$user->getEmail()],201);
    }


    /**
     * @Rest\Get("/api/test", name="get_test")
     */
    public function test(){
        dd('test');
    }
}
