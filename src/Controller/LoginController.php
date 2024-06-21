<?php

namespace App\Controller;

use App\Form\LoginType;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{

    /**
     * @Route("/login", name="app_login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $form  = $this->createForm(LoginType::class);

        $form->handleRequest($request);

        return $this->render('login/index.html.twig', [
            'form'  => $form->createView(),
            'error' => $error,
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new LogicException();
    }
}
