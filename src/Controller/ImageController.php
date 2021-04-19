<?php


namespace App\Controller;

use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{

    /**
     * @Route("/task/new/image", name="upload_image", methods={"POST"})
     */
    public function requestImage(Request $request): Response
    {
        $uploadedFile = $request->files->get("image");

        $destination = $this->getParameter('kernel.project_dir') . "\public\uploads";

        //extenstion:
        //Urlizer - to get clear names
        $orginalFileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME); //without file extension

        //nowa nazwa dla pliku
        $newFileName = Urlizer::urlize( $orginalFileName ). '-' . uniqid() . '.' . $uploadedFile->guessExtension();

        dd($uploadedFile->move(
            $destination,
            $newFileName
        ));

    }

}