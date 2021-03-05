<?php

namespace App\Controller;

use App\Services\VideoUploader;
use App\Entity\Lesson;
use App\Helper\EscapeHelper;
use App\Entity\Course;
use App\Entity\Section;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\SerializerInterface;


class LessonController extends AbstractFOSRestController
{
    /**
     * @var EscapeHelper
     */
    private EscapeHelper $escapeHelper;

    /**
     * @var VideoUploader
     */
    private VideoUploader $videoUploader;


    /**
     * LessonController constructor.
     */
    public function __construct(EscapeHelper $escapeHelper,VideoUploader $videoUploader)
    {
        $this->escapeHelper=$escapeHelper;
        $this->videoUploader=$videoUploader;
    }


    /**
     * @Rest\Post("/api/{course}/{section}/lesson", name="app_course_section_lesson")
     * @Rest\RequestParam(name="name")
     * @Rest\RequestParam(name="description")
     * @Rest\RequestParam(name="isFree")
     * @Rest\RequestParam(name="inOrder")
     * @IsGranted("COURSE_OWNER", subject="course",message="you have no access to update this course", statusCode=403)
     * @param ParamFetcherInterface $fetcher
     * @param SerializerInterface $serializer
     * @param Course $course
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function postCourse(ParamFetcherInterface $fetcher,SerializerInterface $serializer,Course $course,Section $section): JsonResponse
    {
        $data=$fetcher->all();
        $data=$this->escapeHelper->arrayEscape($data);

        /** @var Lesson $lesson */
        $lesson = $serializer->denormalize($data,Lesson::class);
        $lesson->setSection($section);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($lesson);
        $entityManager->flush();

        return new JsonResponse(['lessonId'=>$lesson->getId()],201);
    }


    /**
     * @Rest\Post("/api/{course}/{section}/{lesson}/video", name="app_course_section_lesson")
     * @Rest\FileParam(name="video")
     * @IsGranted("COURSE_OWNER", subject="course",message="you have no access to update this course", statusCode=403)
     * @param ParamFetcherInterface $fetcher
     * @param SerializerInterface $serializer
     * @param Course $course
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function postVideo(ParamFetcherInterface $fetcher,SerializerInterface $serializer,Course $course,Lesson $lesson){

        $data=$fetcher->all();

        $video=$this->videoUploader->uploadVideo($data['video'],$course->getId());
        $video->setLesson($lesson);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($video);
        $entityManager->flush();
        return new JsonResponse('',201);

    }
}
