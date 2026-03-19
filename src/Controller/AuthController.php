<?php

namespace App\Controller;

use App\Security\KeycloakAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthController extends AbstractController
{
    #[Route('/login', name: 'app_login', methods: ['GET'])]
    public function login(Request $request, KeycloakAuthenticator $authenticator): Response
    {
        return $authenticator->start($request);
    }
}
