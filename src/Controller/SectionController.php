<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Section;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;

class SectionController extends AbstractFOSRestController
{
    /**
     * @Rest\Post("/api/{course}/section", name="app_course_section")
     * @Rest\RequestParam(name="name")
     * @Rest\RequestParam(name="description")
     * @IsGranted("COURSE_OWNER", subject="course",message="you have no access to update this course", statusCode=403)
     * @param ParamFetcherInterface $fetcher
     * @param SerializerInterface $serializer
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function postCourse(ParamFetcherInterface $fetcher,SerializerInterface $serializer,Course $course): JsonResponse
    {
        $data=$fetcher->all();
        $data['name']=htmlspecialchars($data['name'],ENT_QUOTES);
        $data['description']=htmlspecialchars($data['description'],ENT_QUOTES);

        /** @var Section $section */
        $section = $serializer->denormalize($data,Section::class);
        $section->setCourse($course);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($section);
        $entityManager->flush();

        return new JsonResponse(['sectionId'=>$section->getId()],201);
    }
}
