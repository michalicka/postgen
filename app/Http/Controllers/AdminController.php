<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('app', [
            'app' => 'admin',
        ]);
    }

    public function sites()
    {
        return view('app', [
            'app' => 'sites',
        ]);
    }
}
