<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController
{

    /**
     * @Route("/task/new/image", name="upload_image", methods={"POST"})
     * @param Request $request
     */
    public function requestImage(Request $request){
        dd($request->files->get("image"));


    }

}