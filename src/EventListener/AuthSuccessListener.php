<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

final class AuthSuccessListener
{
    #public function __construct(private NotficationService $notficationService)
    #{
        
    #}
    #[AsEventListener(event: 'security.authentication.success')]
    public function onSecurityAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
       $authToken = $event->getAuthenticationToken();

       /** @var User $user */

       $user = $authToken->getUser();

       echo "User {$user->getFirstname()} has successfully logged in at " . date('Y-m-d H:i:s') . " avec ce statut de vérification {$user->isVerified()} "."\n";

    }
}
