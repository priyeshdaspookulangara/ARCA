<?php

namespace Modules\AuthMgt\Infrastructure\Facades;

use Illuminate\Support\Facades\Facade;

class AuthMgt extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'authmgt';
    }
}