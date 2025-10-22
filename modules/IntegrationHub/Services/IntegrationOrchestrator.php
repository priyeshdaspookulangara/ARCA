<?php

namespace Modules\IntegrationHub\Services;

use Modules\IntegrationHub\Models\IntegrationProfile;
use Modules\IntegrationHub\Services\Adapter\RestAdapter;
use Modules\IntegrationHub\Services\Adapter\SoapAdapter;
use Modules\IntegrationHub\Events\IntegrationRequestDispatched;
use Modules\IntegrationHub\Events\IntegrationResponseReceived;
use Modules\IntegrationHub\Events\IntegrationFailed;
use Modules\IntegrationHub\Services\CredentialVaultService;
use Modules\IntegrationHub\Models\IntegrationLog;

class IntegrationOrchestrator
{
    protected $restAdapter;
    protected $soapAdapter;
    protected $credentialVault;

    public function __construct(RestAdapter $restAdapter, SoapAdapter $soapAdapter, CredentialVaultService $credentialVault)
    {
        $this->restAdapter = $restAdapter;
        $this->soapAdapter = $soapAdapter;
        $this->credentialVault = $credentialVault;
    }

    public function dispatch(IntegrationProfile $profile, array $payload)
    {
        $log = IntegrationLog::create([
            'direction' => 'outbound',
            'endpoint' => $profile->config['url'] ?? '',
            'status_code' => 'pending',
            'payload' => json_encode($payload),
            'response' => '',
            'timestamp' => now(),
        ]);

        event(new IntegrationRequestDispatched($profile, $payload, $log->id));

        try {
            $decryptedConfig = $this->credentialVault->retrieve($profile->config);
            $profile->config = $decryptedConfig;
            $adapter = $this->getAdapter($profile->type);
            $response = $adapter->send($profile, $payload);
            event(new IntegrationResponseReceived($profile, $response, $log->id));
            return $response;
        } catch (\Exception $e) {
            event(new IntegrationFailed($profile, $e, $log->id));
            throw $e;
        }
    }

    protected function getAdapter($type)
    {
        switch ($type) {
            case 'rest':
                return $this->restAdapter;
            case 'soap':
                return $this->soapAdapter;
            default:
                throw new \Exception("Adapter not found for type: $type");
        }
    }
}
