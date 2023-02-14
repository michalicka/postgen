<?php

namespace Postgen\Api\Controllers;

use Illuminate\Routing\Controller;
use Postgen\Common\Logic\Categories;

class MetaApiController extends Controller
{
    public function dropdowns()
    {
        $dropdowns = [
            'categories' => Categories::list(),
        ];

        return response(compact('dropdowns'));
    }
}
