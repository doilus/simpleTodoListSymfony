<?php


namespace App\Services\Generator;


use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AddImage
{

    private string $uploadPath;
    public function __construct(
        string $uploadPath
    )
    {
        $this->uploadPath = $uploadPath;
    }

    public function addImage(UploadedFile $uploadedFile) : string{
        $destination = $this->uploadPath . "\images_task";

        //extenstion:
        //Urlizer - to get clear names
        $orginalFileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME); //without file extension

        //nowa nazwa dla pliku
        $newFileName = Urlizer::urlize($orginalFileName) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        $uploadedFile->move($destination, $newFileName);

        return $newFileName;
    }

}