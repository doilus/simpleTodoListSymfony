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
     * @Route("/", name="app_homepage")
     */
    public function show(TaskRepository $repository){

        $now = new \DateTime;
        //odczytanie rekordow
        $tasks = $repository->findBy(array('user' => $this->getUser()), array('dueDate' => 'ASC'));


        return $this->render('list/show.html.twig',  [
            'tasks' => $tasks,
            'dateNow' => $now,
        ]);

    }

    /**
     * @Route("/new", name="app_show_tasks")
     */
    public function new(EntityManagerInterface $entityManager, Request $request): Response{
/*
        $task = new Task();
        $task->setName('Do task list')
            ->setSlug('do-task-list'.rand(1,1000))
            ->setTask(<<<EOF
potrzebuję programu, który pozwoli mi wpisać rzeczy, które mam wykonać i do kiedy mam je wykonać.
Chciałbym także zobaczyć w aplikacji zadania, których czas już minął.
Przydałoby się także abym moi przyjaciele mogli także korzystać z aplikacji a co za tym idzie aby mogli założyć sobie konto.
Jednak mimo, że jesteśmy paczką przyjaciół to jednak żaden z nas nie chciałby aby ktoś inny widział nasze zadania, proszę wziąć to pod uwagę.
EOF
)
            ->setDueDate(new \DateTime((sprintf('-%d days',rand(1,100)))));
        /*
        //dodanie
        $form = $this->createFormBuilder($task)
            ->add('name', TextType::class)
            ->add('slug', TextType::class)
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->add('save', SubmitType::class, ['label' => 'Create Task'])
            ->getForm();
*/
        $form = $this->createForm(TaskType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            //dd($form->getData());
            $task = $form->getData();
            //$task = new Task();
            $task->setUser($this->getUser());   //security -->pobieram

            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute("app_homepage");
        }

        //handle the form
        //$form->handleRequest($request);
        /*if($form->isSubmitted() && $form->isValid()){
            $task=$form->getData();
           // dd($form->getData());


            return $this->redirectToRoute('app_homepage', [], 301);
        }*/


        return $this->render('list/form.html.twig', [
            'form' => $form->createView(),
        ]);

        //should be redirect
        //return $this->redirectToRoute('app_show_tasks');
        /*
        return new Response(sprintf(
           'You did it! Added new task id: #%d, name: %s',
           $task->getId(),
           $task->getName()
        ));
        */
    }
}