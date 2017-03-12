<?php

namespace App\Http\Controllers;

use App\Telegram\Update;
use App\Telegram\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $update = new Update($request->getContent());
        $webhook = new Webhook($update);

        Log::info((string) $update);

        if (!$webhook->isAuthorizedUser()) {
            return $webhook->sendMessage('Huh?');
        }

        if ($webhook->isCommand()) {
            return $webhook->runCommandHandler();
        }

        return $webhook->sendMessage('Hello World!');
    }
}
