<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Telegram\Update;

class UpdateTest extends TestCase
{
    public function testIsCommand()
    {
        $update = new Update($this->getCommandUpdate());

        $this->assertTrue($update->isCommand());
    }

    public function testIsNotCommand()
    {
        $update = new Update($this->getTextUpdate());

        $this->assertFalse($update->isCommand());
    }

    public function testIsEdited()
    {
        $update = new Update($this->getEditedUpdate());

        $this->assertTrue($update->isEdited());
    }

    public function testGetUserId()
    {
        $update = new Update($this->getTextUpdate());

        $this->assertEquals(347525401, $update->getUserId());
    }

    public function testGetChatId()
    {
        $update = new Update($this->getTextUpdate());

        $this->assertEquals(347525401, $update->getChatId());
    }

    public function testGetTest()
    {
        $update = new Update($this->getTextUpdate());

        $this->assertEquals('Hello World', $update->getText());
    }

    public function testToString()
    {
        $rawUpdate = $this->getTextUpdate();

        $update = new Update($rawUpdate);

        $this->assertEquals($rawUpdate, (string) $update);
    }

    protected function getTextUpdate(): string
    {
        return file_get_contents(__DIR__ . '/../fixtures/text.json');
    }

    protected function getCommandUpdate(): string
    {
        return file_get_contents(__DIR__ . '/../fixtures/command.json');
    }

    protected function getEditedUpdate(): string
    {
        return file_get_contents(__DIR__ . '/../fixtures/edited.json');
    }
}
