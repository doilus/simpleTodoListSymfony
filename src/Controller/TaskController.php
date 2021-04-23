<?php


namespace App\Controller;


use App\Entity\Image;
use App\Entity\Task;
use App\Form\Type\TaskType;
use App\Repository\ImageRepository;
use App\Repository\TaskRepository;
use App\Services\Generator\GetImageName;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class TaskController extends AbstractController
{
    private string $uploadPath;

    private TaskRepository $taskRepository;

    private GetImageName $getImageName;

    private ImageRepository $imageRepository;


    public function __construct(
        string $uploadPath,
        TaskRepository $taskRepository,
        GetImageName $getImageName,
        ImageRepository $imageRepository

    )
    {
        $this->uploadPath = $uploadPath;
        $this->taskRepository = $taskRepository;
        $this->getImageName = $getImageName;
        $this->imageRepository = $imageRepository;
    }

    /**
     * @Route("/task", name="task", methods={"GET"})
     */
    public function show(): Response
    {
        $tasks = $this->taskRepository->findBy([
            'user' => $this->getUser()
        ], [
            'dueDate' => 'ASC'
        ]);

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
     * @Route("/task/new", name="task_new", methods={"GET", "POST"})
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

            if ($uploadedFile) {
                $baseFileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_EXTENSION);
                $newFileName = $this->getImageName->getImageName($uploadedFile);

                $image = new Image(
                    $baseFileName,
                    $newFileName,
                    $this->getParameter('official_destination_task_files'),
                    $task,
                    $uploadedFile->getSize()
                );

                $this->imageRepository->save($image);
            }

            $this->addFlash('success', 'Task was created!');

            return $this->redirectToRoute("task");
        }

        return $this->render('list/new_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/task/{id}/edit", name="task_edit", methods={"GET", "POST"})
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
            $officialDestination = $this->getParameter('official_destination_task_files');
            $serverDestination = $this->uploadPath . $officialDestination;

            if ($uploadedFile) {
                $baseFileName = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFileName = $this->getImageName->getImageName($uploadedFile);

                $size = $uploadedFile->getSize(); // <--- MUSI być przed move

                $uploadedFile->move($serverDestination, $newFileName);

                $image = new Image(
                    $baseFileName,
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

            return $this->redirectToRoute("task");
        }

        return $this->render('list/edit_form.html.twig', [
            'form' => $form->createView(),
            'images' => $task->getImagesId(),
        ]);
    }

    /**
     * @Route("/task/{id}/delete", name="task_delete", methods={"DELETE"})
     */
    public function delete(int $id): Response
    {
        $task = $this->taskRepository->find($id);

        if(!$task){
            dd();
        }

        $this->taskRepository->delete($task);

        return $this->redirectToRoute("task");
    }


}