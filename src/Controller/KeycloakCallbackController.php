<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class KeycloakCallbackController extends AbstractController
{
    #[Route('/auth/callback', name: 'app_auth_callback', methods: ['GET'])]
    public function __invoke(): Response
    {
        throw new \LogicException('Diese Route wird vom KeycloakAuthenticator verarbeitet.');
    }
}
