<?php

namespace Modules\Fina\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class EventListenerController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Event received:', $request->all());

        return response()->json(['status' => 'success']);
    }
}
