<?php

namespace PostGen\Wizard\Controllers;

use Illuminate\Routing\Controller;

class WizardController extends Controller
{
    public function index()
    {
        return view('app', [
            'app' => 'wizard',
        ]);
    }
}
