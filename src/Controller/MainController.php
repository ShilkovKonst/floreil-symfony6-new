<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(CategoryRepository $categories): Response
    {
        return $this->render('main/index.html.twig', [
            'user' => $this->getUser(),
            'categories' => $categories->findBy([]),
        ]);
    }
}
