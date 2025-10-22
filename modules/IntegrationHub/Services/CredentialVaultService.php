<?php

namespace Modules\IntegrationHub\Services;

use Illuminate\Support\Facades\Crypt;

class CredentialVaultService
{
    public function store(array $credentials)
    {
        return Crypt::encryptString(json_encode($credentials));
    }

    public function retrieve($encryptedCredentials)
    {
        return json_decode(Crypt::decryptString($encryptedCredentials), true);
    }
}
