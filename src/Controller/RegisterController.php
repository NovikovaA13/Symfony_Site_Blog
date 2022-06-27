<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;
use App\Service\CodeGenerator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Event\RegisteredUserEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;


class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(
        UserPasswordEncoderInterface $passwordEncoder,
        Request $request,
        CodeGenerator $codeGenerator,
        EventDispatcherInterface $eventDispatcher
    ): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $codeConfirmation = $codeGenerator->generateCode();
            $user->setConfirmationCode($codeConfirmation);
            $registeredUserEvent = new RegisteredUserEvent($user);
            $eventDispatcher->dispatch($registeredUserEvent, RegisteredUserEvent::NAME);
            $this->addFlash('success', "Un email avec le code de la confirmation a été envoyé à votre email. Veuillez cliquer sur le lien avec le code de la confirmation.");
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('all_posts');
        }
        return $this->render('security/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/confirm/{code}", name="email_confirmation")
     */
    public function confirmEmail(string $code)
    {
        /** var User $user */
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['confirmationCode' => $code]);
        if ($user === null) {
            return new Response('404');
        }
        $user->setEnable(true);
        $user->setPlainPassword('');
        $user->setConfirmationCode('');
        $em = $this->getDoctrine()->getManager();
        $em->flush();
        return $this->render('security/account_confirm.html.twig', ['user' => $user]);
    }
    /**
     * @Route("/email", name="email")
     */
    public function email()
    {
        // Create the Transport
        $transport = (new \Swift_SmtpTransport('localhost', 25))
            ->setUsername('symfony1234567@gmail.com')
            ->setPassword('Sy12345678')
        ;

// Create the Mailer using your created Transport
        $mailer = new Swift_Mailer($transport);

// Create a message
        $message = (new \Swift_Message('Wonderful Subject'))
            ->setFrom(['symfony1234567@gmail.com' => 'Symfony'])
            ->setTo('novikova.a13@gmail.com')
            ->setBody('Here is the message itself')
        ;

// Send the message
        $result = $mailer->send($message);
    }

}
