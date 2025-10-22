<?php

namespace Modules\IntegrationHub\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\IntegrationHub\Models\IntegrationLog;

class LogController extends Controller
{
    public function index()
    {
        return IntegrationLog::all();
    }
}
