<?php

namespace Modules\IntegrationHub\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\IntegrationHub\Services\IntegrationOrchestrator;
use Modules\IntegrationHub\Models\IntegrationProfile;
use Modules\IntegrationHub\Services\CredentialVaultService;

class IntegrationController extends Controller
{
    protected $orchestrator;
    protected $credentialVault;

    public function __construct(IntegrationOrchestrator $orchestrator, CredentialVaultService $credentialVault)
    {
        $this->orchestrator = $orchestrator;
        $this->credentialVault = $credentialVault;
    }

    public function profiles()
    {
        return IntegrationProfile::all();
    }

    public function createProfile(Request $request)
    {
        // Validation logic here
        $config = $request->input('config');
        $encryptedConfig = $this->credentialVault->store($config);
        $profile = IntegrationProfile::create(array_merge($request->except('config'), ['config' => $encryptedConfig]));
        return response()->json($profile, 201);
    }

    public function dispatch(Request $request)
    {
        $profile = IntegrationProfile::findOrFail($request->input('profile_id'));
        $payload = $request->input('payload');
        $response = $this->orchestrator->dispatch($profile, $payload);
        return response()->json($response);
    }
}
