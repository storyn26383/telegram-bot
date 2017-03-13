<?php

namespace App\Telegram;

use Telegram\Bot\Actions;
use Telegram\Bot\Laravel\Facades\Telegram;

class Webhook
{
    protected $update;
    protected $whitelist = [347525401, 317324505];

    public function __construct(Update $update)
    {
        $this->update = $update;
    }

    public function getUpdate()
    {
        return $this->update;
    }

    public function isAuthorizedUser(): bool
    {
        return in_array($this->update->getUserId(), $this->whitelist);
    }

    public function isCommand(): bool
    {
        return $this->update->isCommand();
    }

    public function isEdited(): bool
    {
        return $this->update->isEdited();
    }

    public function runCommandHandler()
    {
        return Telegram::commandsHandler(true);
    }

    public function sendChatAction($action)
    {
        return Telegram::sendChatAction([
            'chat_id' => $this->update->getChatId(),
            'action' => $action,
        ]);
    }

    public function sendMessage($text)
    {
        $this->sendChatAction(Actions::TYPING);

        return Telegram::sendMessage([
            'chat_id' => $this->update->getChatId(),
            'text' => $text,
        ]);
    }
}
