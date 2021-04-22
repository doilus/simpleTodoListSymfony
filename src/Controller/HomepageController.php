<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage", methods={"GET"})
     */
    public function index(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('tasks');
        }

        return $this->render('general/homepage.html.twig');
    }
}