<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class UserController extends AbstractController
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }
    /**
     * @Route("/user/all", name="all_user")
     */
    public function alluser(): Response
    {
        $users = $this->repository->findUsersOfRoles(['ROLE_USER', 'IS_BANNED']);
        return $this->render('user/alluser.html.twig', [
            'users' => $users
        ]);
    }
    /**
     * @Route("/user/delete/{id}", name="user_delete")
     */
    public function user_delete(int $id): Response
    {
        $user = $this->repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', "L'utilisateur est supprimé");
        return $this->redirectToRoute('all_user');
    }

    /**
     * @Route("/user/bann/{id}", name="user_bann")
     */
    public function user_bann(int $id): Response
    {
        $user = $this->repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $user->setRoles(['IS_BANNED']);
        $em->persist($user);
        $em->flush();
        $this->addFlash('success', "L'utilisateur est banni");
        return $this->redirectToRoute('all_user');
    }

    /**
     * @Route("/user/debann/{id}", name="user_debann")
     */
    public function user_debann(int $id): Response
    {
        $user = $this->repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $user->setRoles(['ROLE_USER']);
        $em->persist($user);
        $em->flush();
        $this->addFlash('success', "L'utilisateur est débanni");
        return $this->redirectToRoute('all_user');
    }
}
