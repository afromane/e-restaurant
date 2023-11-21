<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'isHome' => true,
        ]);
    }
    /**
     * @Route("/about", name="app_about")
     */
    public function about(): Response
    {
        return $this->render('home/about.html.twig', [
            'isHome' => false,
        ]);
    }
     /**
     * @Route("/menu", name="app_menu")
     */
    public function menu(): Response
    {
        return $this->render('home/menu.html.twig', [
            'isHome' => false,
        ]);
    }
}
