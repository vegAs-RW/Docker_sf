<?php

namespace App\Controller;

use App\Repository\RabbitRepository;
use App\Service\Greetings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(RabbitRepository $rabbitRepository): Response
    {
        return $this->render('default/index.html.twig', [
            'rabbits' => $rabbitRepository->findAll(),
        ]);
    }

    #[Route('/hello/{name}', name: 'app_hello')]
    public function hello(Greetings $greetingsService, string $name): Response
    {
        return $this->render('default/hello.html.twig', [
            'message' => $greetingsService->greet($name),
        ]);
    }
}
