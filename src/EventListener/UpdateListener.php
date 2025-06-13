<?php

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\Events;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;

#[AsEventListener(event: Events::preUpdate, method: 'preUpdate')]
final readonly class UpdateListener
{

    public function preUpdate(PreUpdateEventArgs $event): void
    {

        $entity = $event->getObject();

        if (!$entity instanceof User) {
            return; // ignore other entities
        }

        // On met à jour la date de modification
        $entity->setUpdatedAt(new \DateTime());
        
    }
}