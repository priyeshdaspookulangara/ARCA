<?php

namespace Modules\Payments\Services\Gateway;

use Illuminate\Http\Request;

class RazorpayService
{
    /**
     * Verifies the webhook signature from Razorpay.
     *
     * @param Request $request
     * @return bool
     */
    public function verifyWebhookSignature(Request $request): bool
    {
        $signature = $request->header('X-Razorpay-Signature');
        $payload = $request->getContent();
        $secret = config('services.razorpay.secret');

        if (empty($signature) || empty($secret)) {
            return false;
        }

        try {
            $expectedSignature = hash_hmac('sha256', $payload, $secret);

            return hash_equals($expectedSignature, $signature);
        } catch (\Exception $e) {
            // Log the error
            return false;
        }
    }
}
