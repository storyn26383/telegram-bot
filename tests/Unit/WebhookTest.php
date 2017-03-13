<?php

namespace Tests\Unit;

use Mockery as m;
use Tests\TestCase;
use App\Telegram\Update;
use App\Telegram\Webhook;
use Telegram\Bot\Laravel\Facades\Telegram;

class WebhookTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testWhitelist()
    {
        $update = m::mock(Update::class);
        $update->shouldReceive('getUserId')->andReturn(347525401);

        $webhook = new Webhook($update);

        $this->assertTrue($webhook->isAuthorizedUser());
    }

    public function testIsCommand()
    {
        $update = m::mock(Update::class);
        $update->shouldReceive('isCommand')->andReturn(true);

        $webhook = new Webhook($update);

        $this->assertTrue($webhook->isCommand());
    }

    public function testGetUpdate()
    {
        $update = m::mock(Update::class);

        $webhook = new Webhook($update);

        $this->assertEquals($update, $webhook->getUpdate());
    }

    public function testSendChatAction()
    {
        $update = m::mock(Update::class);
        $update->shouldReceive('getChatId')->andReturn(347525401);

        $telegramResponse = m::mock(TelegramResponse::class);
        $telegramResponse->shouldReceive('getHttpStatusCode')->andReturn(200);

        Telegram::shouldReceive('sendChatAction')->andReturn($telegramResponse);

        $webhook = new Webhook($update);

        $response = $webhook->sendChatAction('typing');

        $this->assertEquals(200, $response->getHttpStatusCode());
    }

    public function testSendMessage()
    {
        $update = m::mock(Update::class);
        $update->shouldReceive('getChatId')->andReturn(347525401);

        $telegramResponse = m::mock(TelegramResponse::class);
        $telegramResponse->shouldReceive('getHttpStatusCode')->andReturn(200);

        Telegram::shouldReceive('sendChatAction')->andReturn($telegramResponse);
        Telegram::shouldReceive('sendMessage')->andReturn($telegramResponse);

        $webhook = new Webhook($update);

        $response = $webhook->sendMessage('Hello World');

        $this->assertEquals(200, $response->getHttpStatusCode());
    }

    public function testRunCommandHandler()
    {
        Telegram::shouldReceive('commandsHandler')->andReturn(m::mock(\App\Telegram\Update::class));

        $webhook = new Webhook(m::mock(Update::class));

        $this->assertInstanceOf(\App\Telegram\Update::class, $webhook->runCommandHandler());
    }
}
