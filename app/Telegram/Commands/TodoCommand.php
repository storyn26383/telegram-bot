<?php

namespace App\Telegram\Commands;

use App\Todo;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TodoCommand extends Command
{
    protected $name = 'todo';
    protected $description = 'Todo list';

    protected $markdowns = ['help'];

    public function handle($arguments)
    {
        preg_match('/^(create|list|delete|update|help)?\s?(.*)?$/', $arguments, $matches);

        $method = $matches[1] ?: 'help';
        $payload = $matches[2];

        $this->replyWithChatAction(['action' => Actions::TYPING]);

        try {
            $text = $this->$method($payload);
        } catch (ModelNotFoundException $e) {
            $id = implode(', ', $e->getIds());
            $text = "Todo [{$id}] not exists.";
        }

        $message = compact('text');

        if (in_array($method, $this->markdowns)) {
            $message = array_merge($message, [
                'parse_mode' => 'markdown',
            ]);
        }

        $this->replyWithMessage($message);

        return $text;
    }

    public function getHelp(): string
    {
        $help  = 'Usage of /todo command:' . PHP_EOL;
        $help .= PHP_EOL;

        $help .= '*/todo help*' . PHP_EOL;
        $help .= 'Show this message.' . PHP_EOL;
        $help .= PHP_EOL;

        $help .= '*/todo list*' . PHP_EOL;
        $help .= 'List all todos.' . PHP_EOL;
        $help .= PHP_EOL;

        $help .= '*/todo create <title>*' . PHP_EOL;
        $help .= 'Create a new todo.' . PHP_EOL;
        $help .= PHP_EOL;

        $help .= '*/todo update <id> <title>*' . PHP_EOL;
        $help .= 'Update a todo for the given ID.' . PHP_EOL;
        $help .= PHP_EOL;

        $help .= '*/todo delete <id>*' . PHP_EOL;
        $help .= 'Delete a todo for the given ID.' . PHP_EOL;

        return $help;
    }

    protected function create($title): string
    {
        Todo::create(compact('title'));

        return 'Create a todo successfully.';
    }

    protected function list(): string
    {
        $todos = Todo::all();

        if (!$todos->count()) {
            return 'Congratulations! There is no todo.';
        }

        return $todos->reduce(function ($text, $todo) {
            return $text . PHP_EOL . "[{$todo->id}] {$todo->title}";
        }, 'Todo list:');
    }

    protected function delete($id): string
    {
        Todo::findOrFail($id)->delete();

        return 'Delete a todo successfully.';
    }

    protected function update($payload): string
    {
        preg_match('/^(\w+)\s+(.*)?$/', $payload, $matches);

        $id = $matches[1];
        $title = $matches[2];

        Todo::findOrFail($id)->update(compact('title'));

        return 'Update a todo successfully.';
    }

    protected function help(): string
    {
        return $this->getHelp();
    }
}
