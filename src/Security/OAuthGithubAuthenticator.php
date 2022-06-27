<?php

namespace App\Security;

use App\Event\CreatedUserEvent;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2Client;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OAuthGithubAuthenticator extends SocialAuthenticator
{
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var EventDispatcher
     */
    private $eventDispacher;

    public function __construct
    (
        ClientRegistry $clientRegistry,
        EntityManagerInterface $em,
        RouterInterface $router,
        UserRepository $userRepository
    )
    {
        $this->clientRegistry = $clientRegistry;
        $this->em = $em;
        $this->router = $router;
        $this->userRepository = $userRepository;
    }

    /**
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('/connect/', Response::HTTP_TEMPORARY_REDIRECT);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'github_auth';
    }

    /**
     * @param Request $request
     * @return AccessToken|mixed
     */
    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGithubClient());
    }

    /**
     * @return OAuth2ClientInterface
     */
    private function getGithubClient()
    {
        return $this->clientRegistry->getClient('github');
    }
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        /** @var GithubResourceOwner $githubUser */
        $githubUser = $this->getGithubClient()->fetchUserFromToken($credentials);
        $clientId = $githubUser->getId();

        /** @var User $existingUser */
        $existingUser = $this->userRepository->findOneBy(['clientId' => $clientId]);
        if ($existingUser) {
            return $existingUser;
        }
        $githubUserData = $githubUser->toArray();
        var_dump($githubUserData);
        var_dump($clientId);
        $user = User::fromGithubRequest(
            (string) $clientId,
            $githubUserData['email'] ?? $githubUserData['login'],
            $githubUserData['name'] ?? $githubUserData['login']
        );
        $this->em->persist($user);
        $this->em->flush();

        return $user;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response('Authentification failed', Response::HTTP_FORBIDDEN);
    }
    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?Response
    {
        return new RedirectResponse($this->router->generate('all_posts'));
    }

}