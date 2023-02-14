<?php

namespace Postgen\Moderator\Controllers;

use Illuminate\Routing\Controller;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit(int $id)
    {
        return view('app', [
            'app' => 'edit',
            'id' => $id,
            'api_url' => env('API_URL'),
            'api_user' => env('API_USER'),
        ]);
    }
}
