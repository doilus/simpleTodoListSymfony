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

class ListController extends AbstractController
{
    /**
     * @Route("/task", name="app_show_tasks")
     */
    public function show(TaskRepository $repository){

        //odczytanie rekordow
        $tasks = $repository->findAll();

        return $this->render('list/show.html.twig', [
            'tasks' => $tasks,

        ]);
    }

    /**
     * @Route("task/form", name="app_form_add")
     */
    public function form(){
        return $this->render('list/form.html.twig');
    }

    /**
     * @Route("task/form/new", name="app_form_add_new")
     */
    public function new(Request $request): Response{

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
        $form = $this->createForm(TaskType::class, $task);

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