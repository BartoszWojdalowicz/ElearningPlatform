<?php

namespace App\Services;

use getID3;
use App\Entity\Course;
use App\Entity\Video;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;

class VideoUploader extends AbstractController
{
    private function checkVideoRequirments(File $video){
        return in_array($video->guessExtension(), ['mp4', 'mpeg-4', 'mov', 'avi']) && $video->getSize() < 209715200;
    }


    public function uploadVideo(File $video,int $courseId){

        if(!$this->checkVideoRequirments($video))return false;

        $getID3= new getID3();
        $videoMetadata=$getID3->analyze($video);

        $guessExtension = $video->guessExtension();
        $newVideo = new Video();
        $directory = $this->getParameter('video_directory')."/".$courseId;
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
        $safeFilename = bin2hex(random_bytes(20));
        $newFilename = $safeFilename. '.' . $guessExtension;

        try {
            $video->move(
                $directory,
                $newFilename
            );
        } catch (FileException $e) {
        }


        $newVideo
            ->setName($newFilename)
            ->setExtension($videoMetadata['fileformat'])
            ->setDuration($videoMetadata['playtime_seconds'])
            ->setDurationString($videoMetadata['playtime_string'])
            ->setFrameRate($videoMetadata['video']['frame_rate'])
            ->setResolutionX($videoMetadata['video']['resolution_x'])
            ->setResolutionY($videoMetadata['video']['resolution_y']);


        return $newVideo;
    }

}