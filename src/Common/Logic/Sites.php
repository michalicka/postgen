<?php

namespace Postgen\Common\Logic;

use Illuminate\Support\Collection;
use Postgen\Moderator\Models\Site;

class Sites
{
    public static function list(): Collection
    {
        return Site::select(['id', 'name'])
            ->where('user_id', auth()->id())
            ->orderBy('name')
            ->get()
            ->map(fn($item) => (object)[
                'code' => $item->id,
                'name' => $item->name,
            ]);
    }

}
