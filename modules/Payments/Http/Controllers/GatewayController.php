<?php

namespace Modules\Payments\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Payments\Services\Gateway\RazorpayService;

class GatewayController extends Controller
{
    protected $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    public function webhook(Request $request, $gateway)
    {
        if ($gateway === 'razorpay') {
            if ($this->razorpayService->verifyWebhookSignature($request)) {
                // Process the webhook payload
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
            }
        }

        // Handle other gateways
    }
}
