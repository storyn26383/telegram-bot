<?php

namespace Tests\Unit\Commands;

use App\Todo;
use Mockery as m;
use Tests\TestCase;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use App\Telegram\Commands\TodoCommand;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TodoTest extends TestCase
{
    use DatabaseMigrations;

    public function testCreateTodo()
    {
        $command = new TodoCommand;

        $response = $command->make(
            $this->getTelegram(),
            'create Hello World',
            $this->getUpdate()
        );

        $this->assertNotNull(Todo::whereTitle('Hello World')->first());
        $this->assertEquals('Create a todo successfully.', $response);
    }

    public function testListTodo()
    {
        $todo = Todo::create(['title' => 'Hello World']);

        $command = new TodoCommand;

        $response = $command->handle('list');

        $expected  = 'Todo list:' . PHP_EOL;
        $expected .= "[{$todo->id}] {$todo->title}";

        $this->assertEquals($expected, $response);
    }

    public function testNoTodo()
    {
        $command = new TodoCommand;

        $response = $command->handle('list');

        $expected  = 'Congratulations! There is no todo.';

        $this->assertEquals($expected, $response);
    }

    public function testDeleteTodo()
    {
        $todo = Todo::create(['title' => 'Hello World']);

        $command = new TodoCommand;

        $response = $command->make(
            $this->getTelegram(),
            "delete {$todo->id}",
            $this->getUpdate()
        );

        $this->assertTrue(Todo::onlyTrashed()->find($todo->id)->trashed());
        $this->assertEquals('Delete a todo successfully.', $response);
    }

    public function testDeleteNotExistsTodo()
    {
        $command = new TodoCommand;

        $response = $command->make(
            $this->getTelegram(),
            "delete xxx",
            $this->getUpdate()
        );

        $this->assertEquals('Todo [xxx] not exists.', $response);
    }

    public function testUpdateTodo()
    {
        $todo = Todo::create(['title' => 'Hello World']);

        $command = new TodoCommand;

        $response = $command->make(
            $this->getTelegram(),
            "update {$todo->id} Hello Kitty",
            $this->getUpdate()
        );

        $this->assertEquals('Hello Kitty', $todo->fresh()->title);
        $this->assertEquals('Update a todo successfully.', $response);
    }

    public function testUpdateNotExistsTodo()
    {
        $command = new TodoCommand;

        $response = $command->make(
            $this->getTelegram(),
            "update xxx xxx",
            $this->getUpdate()
        );

        $this->assertEquals('Todo [xxx] not exists.', $response);
    }

    public function testHelp()
    {
        $command = new TodoCommand;

        $response = $command->make($this->getTelegram(), 'help', $this->getUpdate());

        $this->assertEquals($command->getHelp(), $response);
    }

    public function testDefaltMethodIsHelp()
    {
        $command = new TodoCommand;

        $response = $command->make($this->getTelegram(), '', $this->getUpdate());

        $this->assertEquals($command->getHelp(), $response);
    }

    protected function getTelegram()
    {
        $telegram = m::mock(Api::class);

        $telegram->shouldReceive('sendChatAction');
        $telegram->shouldReceive('sendMessage');

        return $telegram;
    }

    protected function getUpdate()
    {
        $update = m::mock(Update::class);

        $update->shouldReceive('getMessage')->andReturn(
            m::mock('mesaage')->shouldReceive('getChat')->andReturn(
                m::mock('chat')->shouldReceive('getId')->getMock()
            )->getMock()
        );

        return $update;
    }
}
