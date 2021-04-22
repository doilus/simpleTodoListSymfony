<?php


namespace App\Controller;

use App\Entity\Image;
use App\Form\Type\ImageType;
use App\Repository\ImageRepository;
use App\Services\Generator\GetImageName;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    private string $uploadPath;

    private ImageRepository $imageRepository;

    public function __construct(
        string $uploadPath,
        ImageRepository $imageRepository
    )
    {
        $this->uploadPath = $uploadPath;
        $this->imageRepository = $imageRepository;
    }

    /**
     * @Route("/image/{id}/download", name="download_image", methods={"GET"})
     */
    public function downloadImage(Image $image): Response
    {
        $file = $this->uploadPath . '/' . $image->getOfficialDestination();

        $response = new BinaryFileResponse($file);

        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $image->getClientNameWithExtension());

        return $response;
    }


    /**
     * @Route("/image/{id}/edit", name="image_edit", methods={"POST", "GET"})
     */
    public function editImage(Request $request, Image $image = null): Response
    {
        if (!$image) {
            return $this->redirectToRoute('app_homepage');
        }

        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newUploadedFile = $form['imageFile']->getData();

            if ($newUploadedFile) {   //jezeli zostalo wybrane
                $newFileName = $form['clientName']->getData();

                $serverDestination = $this->prepareServerDestination();

                $newCreatedFileName = $this->prepareFileName(
                    $newFileName,
                    $newUploadedFile->guessExtension()
                );

                $newUploadedFile->move($serverDestination, $newCreatedFileName);

                $this->removeOldFile($image);

                $newSize = filesize($serverDestination . "/" . $newCreatedFileName);

                $image->setSize($newSize)
                    ->setClientName($newFileName)
                    ->setCreatedName($newCreatedFileName);
            }

            $image = $form->getData();

            $this->imageRepository->save($image);

            $this->addFlash('sucess', 'Image was updated');

            return $this->redirectToRoute('app_edit_task', [
                'id' => $image->getTaskId()->getId()
            ]);
        }


        return $this->render('/image/edit_image_form.html.twig', [
            'form' => $form->createView(),
            'task' => $image->getTaskId()
        ]);
    }

    /**
     * @Route("/image/{id}/delete", name="image_delete", methods={"DELETE"})
     */
    public function deleteImage(Image $image): Response
    {
        $this->imageRepository->delete($image);

        return $this->redirectToRoute('app_edit_task', [
            'id' => $image->getTaskId()->getId()
        ]);
    }

    private function prepareFileName(
        string $newFileName,
        string $fileExtension
    ): string
    {
        return Urlizer::urlize($newFileName) . '-' . uniqid() . '.' . $fileExtension;
    }

    private function prepareServerDestination(): string
    {
        return $this->uploadPath . $this->getParameter('official_destination_task_files');
    }

    private function removeOldFile(Image $image): void
    {
        $oldUploadedFilePath = $this->uploadPath . $image->getOfficialDestination();

        unlink($oldUploadedFilePath);
    }

}