<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener(event: Events::postPersist, method: 'postPersist')]
final readonly class CreateListener
{

    public function postPersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getObject();

        if (!$entity instanceof User) {
            return;
        }

        // Définir la date de création si elle n’est pas déjà définie
        if ($entity->getCreatedAt() === null) {
            $entity->setCreatedAt(new \DateTime());
        }
    }
}