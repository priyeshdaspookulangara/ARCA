<?php

namespace Modules\IntegrationHub\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\IntegrationHub\Events\WebhookReceived;

class WebhookController extends Controller
{
    public function handle(Request $request, $source)
    {
        // Validation and security checks here
        event(new WebhookReceived($source, $request->all()));
        return response()->json(['message' => 'Webhook received']);
    }
}
