<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;

class OAuthController extends AbstractController
{
    /**
     * @param ClientRegistry $clientRegistry
     * @return RedirectResponse
     * @Route("/connect/google", name="connect_google_start")
     */
    public function redirectToGoogleConnect(ClientRegistry $clientRegistry)
    {
        return $clientRegistry->getClient('google')->redirect(['email', 'profile']);
    }

    /**
     * @Route("/google/auth", name="google_auth")
     * @return JsonResponse|RedirectResponse
     */
    public function connectGoogleCheck()
    {
        if (!$this->getUser()) {
            return new JsonResponse(['status' => false, 'message' => 'User not found']);
        }
        else {
            return $this->redirectToRoute('all_posts');
        }
    }

    /**
     * @param ClientRegistry $clientRegistry
     * @Route("/connect/github", name="connect_github_start")
     * @return RedirectResponse
     */
    public function redirectToGithubConnect(ClientRegistry $clientRegistry)
    {
        return $clientRegistry->getClient('github')->redirect(['email', 'public_repo']);
    }

    /**
     * @Route("github/auth", name="github_auth")
     * @return RedirectResponse|Response
     */
    public function connectGithubCheck()
    {
        if (!$this->getUser()) {
            return new Response('User not found', 404);
        } else return $this->redirectToRoute('all_posts');
    }
}
