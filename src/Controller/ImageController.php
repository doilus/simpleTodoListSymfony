<?php


namespace App\Controller;

use App\Entity\Image;
use App\Form\Type\ImageType;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    private ImageRepository $imageRepository;
    private string $uploadPath;
    public function __construct(
        ImageRepository $imageRepository,
        string $uploadPath
    )
    {
        $this->imageRepository = $imageRepository;
        $this->uploadPath = $uploadPath;
    }

    /**
     * @Route("/image/{id}/delete", name="image_delete", methods={"GET"})
     */
    public function deleteImage(Image $image): Response
    {

        $this->imageRepository->delete($image);

        return $this->redirectToRoute('app_edit_task', ['id' => $image->getTaskId()->getId()]);

    }

    /**
     * @Route("/image/{id}/edit", name="image_edit", methods={"POST", "GET"})
     * @param Image $image
     * @return Response
     */
    public function editImage(Image $image, Request $request) : Response{

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);


        return $this->render('/image/edit_image_form.html.twig', [ 'form' => $form->createView(), 'task' => $image->getTaskId()]);
    }

    /**
     * @Route("/image/{id}/download", name="download_image")
     */
    public function downloadImage(Image $image): Response
    {

        $file = $this->uploadPath . '/' . $image->getOfficialDestination();

        //BinaryFileResponse::trustXSendfileTypeHeader();

        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $image->getClientNameWithExtension());


        return $response;
    }

}