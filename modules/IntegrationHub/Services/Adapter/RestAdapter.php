<?php

namespace Modules\IntegrationHub\Services\Adapter;

use Illuminate\Support\Facades\Http;
use Modules\IntegrationHub\Models\IntegrationProfile;

class RestAdapter
{
    public function send(IntegrationProfile $profile, array $payload)
    {
        $config = $profile->config;
        $response = Http::withHeaders($config['headers'])
            ->{$config['method']}($config['url'], $payload);

        return $response->json();
    }
}
