<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: Events::postUpdate, method: 'postUpdate')]
final readonly class CreateUserListener
{

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        $entity->setRoles(["ROLE_USER"]);
        $entity->setIsVerified(false);
        $entity->setIsActive(false);
        $entity->setPoints(0);

        // Définir la date de création si elle n’est pas déjà définie
        if ($entity->getCreatedAt() === null) {
            $entity->setCreatedAt(new \DateTime());
        }
    }
}