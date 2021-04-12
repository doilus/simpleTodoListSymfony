<?php


namespace App\Controller;


use App\Entity\Task;
use App\Form\Type\TaskType;
use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\TextType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
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



            return $this->redirectToRoute("app_homepage");
        }



        return $this->render('list/form.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}