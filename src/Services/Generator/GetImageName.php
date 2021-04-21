<?php


namespace App\Services\Generator;


use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GetImageName
{

    private string $uploadPath;
    public function __construct(
        string $uploadPath
    )
    {
        $this->uploadPath = $uploadPath;
    }

    public function getImageName(UploadedFile $uploadedFile) : string{


        $orginalFileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME); //without file extension

        $newFileName = Urlizer::urlize($orginalFileName) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();


        return $newFileName;
    }

}