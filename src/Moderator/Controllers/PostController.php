<?php

namespace Postgen\Moderator\Controllers;

use Illuminate\Routing\Controller;
use Postgen\Generator\Models\Post;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit(int $id)
    {
        return view('edit', [
            'id' => $id,
        ]);
    }
}
