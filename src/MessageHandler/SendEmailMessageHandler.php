<?php

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class SendEmailMessageHandler{
    public function __invoke(SendEmailMessage $message): void
    {
        // do something with your message
    }
}
