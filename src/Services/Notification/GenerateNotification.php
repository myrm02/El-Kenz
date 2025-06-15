<?php

use App\Entity\Notification;
use App\Entity\User;

abstract class GenerateNotification
{
    /**
     * Generates a notification for a user.
     *
     * @param User $user The user to generate the notification for.
     * @return Notification The generated notification entity.
     */
    public function generate(User $user, string $label) 
    {
        // Create a new Notification entity
        $notification = new Notification();
        
        // Set the user and label for the notification
        $notification->setAccount($user);
        $notification->setLabel($label);
        
        // Return the notification entity
        return $notification;
    }
}