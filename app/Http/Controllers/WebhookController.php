<?php

namespace App\Http\Controllers;

use App\Telegram\Update;
use App\Telegram\Webhook;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class WebhookController extends Controller
{
    public function handle()
    {
        $webhook = $this->prepareWebhook();

        Log::info((string) $webhook->getUpdate());

        if ($webhook->isEdited()) {
            return;
        }

        if (!$webhook->isAuthorizedUser()) {
            return $webhook->sendMessage('Huh?');
        }

        if ($webhook->isCommand()) {
            return $webhook->runCommandHandler();
        }

        return $webhook->sendMessage('Hello World!');
    }

    protected function prepareWebhook()
    {
        return new Webhook(new Update(Request::getContent()));
    }
}
