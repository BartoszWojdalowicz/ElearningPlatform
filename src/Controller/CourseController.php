<?php

namespace App\Controller;

use App\Repository\UserCourseRepository;
use App\Services\PaymentServices;
use App\Entity\UserCourse;
use App\Entity\Course;
use App\Form\CourseType;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;


class CourseController extends AbstractFOSRestController
{
    /**
     * @Rest\Post("/api/course", name="app_course")
     * @Rest\RequestParam(name="name")
     * @Rest\RequestParam(name="description")
     * @Rest\RequestParam(name="price", requirements="^\d+(\.\d{1,2})?$")
     * @param ParamFetcherInterface $fetcher
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function postCourse(ParamFetcherInterface $fetcher,SerializerInterface $serializer): JsonResponse
    {
        $data=$fetcher->all();
        $user=$this->getUser();   // user must be logged because firewall in security.yaml require this.
        $data['name']=htmlspecialchars($data['name'],ENT_QUOTES);
        $data['description']=htmlspecialchars($data['description'],ENT_QUOTES);
        $course = $serializer->denormalize($data,Course::class);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($course);
        $entityManager->flush();

        $this->assignCourseToUser($user, $course,true);

        return new JsonResponse(['courseId'=>$course->getId()],201);
    }

    /**
     * @Rest\Get("/api/course/{course}", name="app_get_course")
     * @param Course $course
     * @return JsonResponse
     */
    public function getCourseDetails(Course $course): JsonResponse
    {
        if(($course->getIsPublic() === false) && !$this->isGranted('COURSE_OWNER', $course)) {
            return new JsonResponse('this course is private', 403);
        }
        return new JsonResponse(['course'=>[
            'id'=>$course->getId(),
            'name'=>$course->getName(),
            'description'=>$course->getDescription(),
            'price'=>$course->getPrice(),
            'rating'=>$course->getRating()]
        ],200);
    }

    /**
     * @Rest\Patch("/api/course/{course}", name="app_put_course")
     * @IsGranted("COURSE_OWNER", subject="course",message="you have no access to update this course", statusCode=403)
     * @param Course $course
     * @param Request $request
     * @return JsonResponse
     */
    public function patchCourseDetails(Course $course,Request $request): JsonResponse
    {
        $form=$this->createForm(CourseType::class,$course); // in patch request data must be submitted in form or every attribute must be check and set alone
        $form->submit($request->request->all(),false);
        if(!$form->isValid()){
            return new JsonResponse('',400);
        }
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return new JsonResponse('ok',201);
    }

    /**
     * @Rest\Delete("/api/course/{course}", name="app_delete_course")
     * @IsGranted("COURSE_OWNER", subject="course",message="you have no access to delete this course", statusCode=403)
     * @param Course $course
     * @return JsonResponse
     */
    public function deleteCourseDetails(Course $course): JsonResponse
    {
        $courseId=$course->getId();
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($course);
        $entityManager->flush();

        return new JsonResponse("course with id $courseId was deleted",200);
    }

    /**
     * @Rest\Post("/api/course/{course}/buy", name="app_buy_course")
     * @param Course $course
     * @param PaymentServices $payment
     * @param UserCourseRepository $repository
     * @return JsonResponse
     */
    public function postBuyCourse(Course $course,PaymentServices $payment,UserCourseRepository $repository): JsonResponse
    {
        $user=$this->getUser();

        if(null === $repository->findOneBy(['course' => $course->getId(), 'user' => $user, 'isBuyer' => true]) &&
            null === $repository->findOneBy(['course' => $course->getId(), 'user' => $user, 'IsOwner' => true])) {
                if ($payment->confirmPayment()) {
                    $this->assignCourseToUser($user, $course);
                    return new JsonResponse(['courseId' => $course->getId()], 201);
                }
            }
            return new JsonResponse('you bought this course or you are the owner',400);
    }

    private function assignCourseToUser(UserInterface $user,Course $course,bool $isOwner=false):bool
    {


        $userCourse= new userCourse();
        $userCourse
            ->setCourse($course)
            ->setIsBuyer(!$isOwner)
            ->setIsOwner($isOwner)
            ->setUser($user);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($userCourse);
        $entityManager->flush();//TODO try catch return
        return true;
    }




}
