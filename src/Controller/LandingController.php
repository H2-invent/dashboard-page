<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LandingController extends AbstractController
{
    public function __construct(
        #[Autowire('%app.landing_links%')]
        private readonly array $landingLinks,
    ) {
    }

    #[Route('/', name: 'app_landing', methods: ['GET'])]
    public function __invoke(): Response
    {
        return $this->render('landing/index.html.twig', [
            'links' => $this->landingLinks,
            'user' => $this->getUser(),
        ]);
    }
}
