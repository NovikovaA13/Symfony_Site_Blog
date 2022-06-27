<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig_Environment;
use App\Entity\User;

class SecurityController extends AbstractController
{
    /**
     * @var \Twig_Environment  $twig
     */
    private $twig;
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }
    /**
     * @Route("login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $user = $authenticationUtils->getLastUsername();
        $error = $authenticationUtils->getLastAuthenticationError();
        return new Response($this->twig->render('security/login.html.twig', [
            'user' => $user,
            'error' => $error
        ]));
    }

    /**
     * @Route("logut", name="logout")
     */
    public function logout()
    {

    }
}
