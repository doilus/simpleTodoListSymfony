<?php

namespace App\Repository;

use App\Entity\Image;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @method Image|null find($id, $lockMode = null, $lockVersion = null)
 * @method Image|null findOneBy(array $criteria, array $orderBy = null)
 * @method Image[]    findAll()
 * @method Image[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImageRepository extends ServiceEntityRepository
{

    public function __construct(
        ManagerRegistry $registry
    )
    {
        parent::__construct($registry, Image::class);
    }

    public function save(Image $image){

        $this->_em->persist($image);
        $this->_em->flush();
    }

    public function delete(Image $image){

        $this->_em->remove($image);
        $this->_em->flush();
    }


}
