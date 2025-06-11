<?php

namespace App\Message;

use DateTime;
use Symfony\Component\Messenger\Attribute\AsMessage;

#[AsMessage('async')]
final class SendNotificationMessage
{
    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

    public function __construct(
        private int $userId,
        private string $name,
        private string $action_type,
        private DateTime $date,
    ) {
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }
    
    public function getActionType(): string
    {
        return $this->action_type;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

}
