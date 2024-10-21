<?php 

namespace App\Service\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    /**
     * Customize the JWT payload.
     */
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        // Get the current payload data from the event
        $payload = $event->getData();

        // Get the authenticated user (App\Entity\User)
        $user = $event->getUser();


    // Set the 'email' field in the JWT payload instead of 'username'
    // Use the email instead of username
    $payload['username'] = $user->getEmail();  

    // Set the modified payload back to the event
    $event->setData($payload);
    }
}