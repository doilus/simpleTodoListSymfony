<?php


namespace App\Controller;


use App\Entity\Image;
use App\Entity\Task;
use App\Form\Type\TaskType;
use App\Repository\ImageRepository;
use App\Repository\TaskRepository;
use App\Services\Generator\AddImage;
use App\Services\Generator\GetImageName;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TaskController extends AbstractController
{


    private TaskRepository $taskRepository;
    private GetImageName $getImageName;
    private ImageRepository $imageRepository;
    private string $uploadPath;

    /**
     * ListController constructor.
     * @param TaskRepository $taskRepository
     */
    public function __construct(
        TaskRepository $taskRepository,
        GetImageName $getImageName,
        ImageRepository $imageRepository,
        string $uploadPath
    )
    {
        $this->taskRepository = $taskRepository;
        $this->getImageName = $getImageName;
        $this->imageRepository = $imageRepository;
        $this->uploadPath = $uploadPath;
    }

    /**
     * @Route("/", name="app_homepage", methods={"GET"})
     */
    public function homepage(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_task');
        }

        return $this->render('general/homepage.html.twig');
    }

    /**
     * @Route("/task", name="app_task", methods={"GET"})
     */
    public function show(): Response
    {
        $tasks = $this->taskRepository->findBy(['user' => $this->getUser()], ['dueDate' => 'ASC']);

        $tasksSoon = [];
        foreach ($tasks as $task) {
            if ($task->checkDateReminder()) {
                $tasksSoon[] = $task;
            }
        }

        return $this->render('list/show_table.html.twig', [
            'tasks' => $tasks,
            'taskSoon' => $tasksSoon,
        ]);

    }

    /**
     * @Route("/task/new", name="app_add_task", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {

        $form = $this->createForm(TaskType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task = $form->getData();
            $task->setUser($this->getUser());

            $this->taskRepository->save($task);


            /**
             * @var UploadedFile $uploadedFile
             */
            $uploadedFile = $form['imageFile']->getData();
            $destination = $this->uploadPath . "/images_task";


            if ($uploadedFile) {
                $newFileName = $this->getImageName->getImageName($uploadedFile, $destination);

                $image = new Image(
                    pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME),
                    $newFileName,
                    5,
                    '\images_task',
                    $task
                );

                $this->imageRepository->save($image);


            }


            $this->addFlash('success', 'Task was created!');

            return $this->redirectToRoute("app_task");
        }

        return $this->render('list/new_form.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @param Task $task
     * @param Request $request
     * @Route("/task/{id}/edit", name="app_edit_task", methods={"GET", "POST"})
     */
    public function edit(Task $task, Request $request): Response
    {
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var UploadedFile $uploadedFile
             */
            $uploadedFile = $form['imageFile']->getData();
            $officialDestination = "/uploads/images_task";
            $serverDestination = $this->uploadPath . $officialDestination;


            if ($uploadedFile) {
                $newFileName = $this->getImageName->getImageName($uploadedFile);

                $uploadedFile->move($serverDestination, $newFileName);

                $size = filesize($serverDestination . "/" . $newFileName);

                $image = new Image(
                    pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME),
                    $newFileName,
                    $officialDestination,
                    $task,
                    $size
                );


                $this->imageRepository->save($image);

            }

            $task = $form->getData();
            $this->taskRepository->save($task);

            $this->addFlash('success', 'Task was updated!');

            return $this->redirectToRoute("app_task");
        }

        return $this->render('list/edit_form.html.twig', [
            'form' => $form->createView(),
            'images' => $task->getImagesId(),
        ]);
    }

    /**
     * @param Task $task
     * @param Request $request
     * @Route("/task/{id}/delete", name="app_delete_task", methods={"DELETE"})
     */
    public function delete(Task $task): Response
    {

        $this->taskRepository->delete($task);

        return $this->redirectToRoute("app_task");
    }


}