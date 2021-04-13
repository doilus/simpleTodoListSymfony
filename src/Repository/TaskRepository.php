<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function save(Task $task){
        $this->_em->persist($task);
        $this->_em->flush();

    }

    public function update(Task $task){
        $this->_em->persist($task);
        $this->_em->flush();
    }

    public function writeToCSV()
    {
        //$rows = [];
        $tasks = $this->findAll();

        $fp =fopen("sample.csv", "w");
        foreach ($tasks as $task){
            $data = [$task->getId(), $task->getName(), $task->getSlug(), $task->getDueDate()->format('Y-m-d H:i:s'), $task->getUser()->getId(), $task->getIsDone(), $task->getTask()];
            //$rows[] = implode(',', $data);
            fputcsv($fp, $data, ',' );
        }

        fclose($fp);
        /*$content = implode('\n', $rows);
        $response = new Response($content);
        $response->headers->set("Content-Type", "text/csv");

        return $content;*/
    }

    /*
    public function findAllTaskUser(User $user): \Doctrine\ORM\QueryBuilder
    {



        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :id')
            ->setParameter('id', $user->getId())
            ;
    }*/
    // /**
    //  * @return Task[] Returns an array of Task objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Task
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
