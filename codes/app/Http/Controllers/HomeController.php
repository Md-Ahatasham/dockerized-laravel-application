<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        Log::channel('elasticsearch')->info('Direct store elastic log', [
            'user_id' => Auth::getUser(),
            'action' => 'login',

        ]);   // direct log to elasticsearch


        Log::channel('stack')->info('Log to file', [
            'user_id' => Auth::getUser(),
            'action' => 'loginOrRegistration',
        ]);   // store logs in file then fluentd read from file to store elasticsearch
        return view('home');
    }
}
