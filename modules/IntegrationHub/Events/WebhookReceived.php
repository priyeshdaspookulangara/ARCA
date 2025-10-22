<?php

namespace Modules\IntegrationHub\Events;

use Illuminate\Queue\SerializesModels;

class WebhookReceived
{
    use SerializesModels;

    public $source;
    public $payload;

    public function __construct($source, array $payload)
    {
        $this->source = $source;
        $this->payload = $payload;
    }
}
