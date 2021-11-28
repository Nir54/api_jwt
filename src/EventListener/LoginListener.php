<?php

namespace App\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;

class LoginListener
{

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        // Get the User entity.
        $user = $event->getAuthenticationToken()->getUser();

        return $user;
        // Update your field here.
        // $user->setLastLogin(new \DateTime());

        // Persist the data to database.
        // $this->em->persist($user);
        // $this->em->flush();
    }
}