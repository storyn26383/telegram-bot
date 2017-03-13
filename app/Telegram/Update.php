<?php

namespace App\Telegram;

class Update
{
    protected $raw;
    protected $update;

    public function __construct($update)
    {
        $this->raw = $update;
        $this->update = json_decode($update);
    }

    public function isCommand(): bool
    {
        return !!preg_match('/^\/([^\s@]+)@?(\S+)?\s?(.*)$/', $this->message->text);
    }

    public function isEdited(): bool
    {
        return array_key_exists('edited_message', $this->update);
    }

    public function getUserId(): int
    {
        return (int) $this->message->from->id;
    }

    public function getChatId(): int
    {
        return (int) $this->message->chat->id;
    }

    public function getText(): string
    {
        return $this->message->text;
    }

    public function __get($key)
    {
        return $this->update->$key;
    }

    public function __toString()
    {
        return $this->raw;
    }
}
