<?php

namespace Postgen\Api\Controllers;

use Illuminate\Routing\Controller;
use Postgen\Moderator\Models\Site;

class SiteApiController extends Controller
{
    public function list()
    {
        $query = Site::select(['id', 'name', 'api_url', 'api_key'])
            ->where('user_id', auth()->id())
            ->orderBy('name');

        return $query->get();
    }

    public function update()
    {
        $id = request('id');
        $user_id = auth()->id();
        $name = request('name');
        $api_url = request('api_url');
        $api_key = request('api_key');

        if (!filter_var($api_url, FILTER_VALIDATE_URL)) {
            abort(422);
        }

        $data = compact('user_id', 'name', 'api_url', 'api_key');

        if ($id) {
            $site = Site::findOrFail($id);
            if (auth()->id() !== 1 && $site->user_id !== auth()->id()) abort(403);
            $site->update($data);
        } else {
            $site = Site::create($data);
        }

        return response(compact('site'));
    }

    public function remove(int $id)
    {
        $site = Site::findOrFail($id);
        if (auth()->id() !== 1 && $site->user_id !== auth()->id()) abort(403);
        $success = $site->delete();

        return response(compact('success'));
    }
}
