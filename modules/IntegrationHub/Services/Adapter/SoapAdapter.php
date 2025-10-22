<?php

namespace Modules\IntegrationHub\Services\Adapter;

use Modules\IntegrationHub\Models\IntegrationProfile;

class SoapAdapter
{
    public function send(IntegrationProfile $profile, array $payload)
    {
        // A more complete implementation would use a SoapClient
        // For now, we'll just return a mock response
        return ['message' => 'SOAP request sent successfully'];
    }
}
