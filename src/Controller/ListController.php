<?php


namespace App\Controller;


use App\Entity\Task;
use App\Form\Type\TaskType;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints\Date;

class ListController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var TaskRepository
     */
    private TaskRepository $repository;

    /**
     * ListController constructor.
     * @param EntityManagerInterface $entityManager
     * @param TaskRepository $repository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TaskRepository $repository
    )
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
    }


    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(){
        return $this->render('base.html.twig');
    }
    /**
     * @Route("/task", name="app_task")
     */
    public function show(){

        // $this->denyAccessUnlessGranted('ROLE_USER'); // <--- also to prevent unlogged user from access (security.yaml)
        $now = new \DateTime;
        //odczytanie rekordow dotyczących TYLKO zalogowanego usera
        $tasks = $this->repository->findBy(['user' => $this->getUser()], ['dueDate' => 'ASC']);


        return $this->render('list/show.html.twig',  [
            'tasks' => $tasks,
            'dateNow' => $now,
        ]);

    }

    /**
     * @Route("/task/new", name="app_add_task")
     */
    public function new(Request $request): Response{

        $form = $this->createForm(TaskType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //dd($form->getData());
            $task = $form->getData();


            $task->setUser($this->getUser());   //security -->pobieram

            $this->repository->save($task);

            //$this->addFlash('success', 'Task was created!');


            return $this->redirectToRoute("app_task");
        }



        return $this->render('list/new_form.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    /**
     * @param Task $task
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @Route("/task/{id}/edit", name="app_edit_task")
     */
    public function edit(Task $task, Request $request){
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $task = $form->getData();

            $this->repository->save($task);

            //$this->addFlash('success', 'Task was updated!');

            return $this->redirectToRoute("app_task");
        }

        return $this->render('list/edit_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}