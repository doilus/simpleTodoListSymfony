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

    private GetImageName $getImageName;

    private ImageRepository $imageRepository;

    public function __construct(
        string $uploadPath,
        GetImageName $getImageName,
        ImageRepository $imageRepository,
    )
    {
        $this->uploadPath = $uploadPath;
        $this->getImageName = $getImageName;
        $this->imageRepository = $imageRepository;
    }

    /**
     * @Route("/image/{id}/download", name="download_image")
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
    public function editImage(Image $image = null, Request $request): Response
    {
        if(!$image){
            return $this->redirectToRoute('homepage');      //jakis inny dla bledu
        }

        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newUploadedFile = $form['imageFile']->getData();

            if ($newUploadedFile) {
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

            return $this->redirectToRoute('task_edit', [
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
    public function deleteImage(int $id): Response
    {
        $image = $this->imageRepository->find($id);
        $this->imageRepository->delete($image);

        return $this->redirectToRoute('task_edit', [
            'id' => $image->getTaskId()->getId()
        ]);

    }

    private function prepareServerDestination(): string
    {
        return $this->uploadPath . $this->getParameter('official_destination_task_files');
    }

    private function prepareFileName($newFileName, $fileExtension): string
    {
        return Urlizer::urlize($newFileName) . '-' . uniqid() . '.' . $fileExtension;
    }

    private function removeOldFile(Image $image)
    {
        $oldUploadedFilePath = $this->uploadPath . $image->getOfficialDestination();

        unlink($oldUploadedFilePath);
    }


}