<?php

namespace App\Http\Controllers;

use Exception;
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

        try {
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
        } catch (Exception $e) {
            return $webhook->sendMessage($e->getMessage());
        }
    }

    protected function prepareWebhook()
    {
        return new Webhook(new Update(Request::getContent()));
    }
}
