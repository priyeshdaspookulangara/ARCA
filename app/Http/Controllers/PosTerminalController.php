<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PosTerminalController extends Controller
{
    /**
     * Show the POS terminal application.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('pos.terminal');
    }
}