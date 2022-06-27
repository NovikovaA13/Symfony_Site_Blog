<?php

namespace App\Service;
use Swift_Mailer;
use Swift_Message;
use Twig_Environment;
use App\Entity\User;

class Mailer
{
    public const FROM_ADRESS = 'symfony1234567@gmail.com';
    /**
     * @var Swift_Mailer
     */
    private $mailer;

    /**
     * @var Twig_Environment
     */
    private $twig;
    public function __construct(
        Swift_Mailer $mailer,
        Twig_Environment $twig
    )
	{
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    /**
     * @param User $user
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function sendConfirmationMessage(User $user)
    {
        $messageBody = $this->twig->render('security/confirmation.html.twig', ['user' => $user]);
        $message = new Swift_Message();
        $message->setSubject("L'inscription est bien reussie")
                ->setTo('novikova.a13@gmail.com')
                ->setFrom(self::FROM_ADRESS)
                ->setBody($messageBody, 'text/html');
        $this->mailer->send($message);
    }
}