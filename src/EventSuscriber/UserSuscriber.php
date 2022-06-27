<?php

namespace App\EventSuscriber;

use App\Service\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use App\Event\RegisteredUserEvent;

class UserSuscriber implements EventSubscriberInterface
{
    /**
     * @var Mailer $mailer
     */
    private $mailer;

    /**
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [RegisteredUserEvent::NAME => 'onUserRegister'];
    }
    public function onUserRegister(RegisteredUserEvent $registeredUserEvent)
    {
        $this->mailer->sendConfirmationMessage($registeredUserEvent->getRegisteredUser());
    }

}