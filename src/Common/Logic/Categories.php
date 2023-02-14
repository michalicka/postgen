<?php

namespace Postgen\Common\Logic;

use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Categories
{
    public static function list(): Collection
    {
        return collect(\DB::select("
            SELECT DISTINCT category
            FROM posts
            WHERE status = ?
              AND published_at <= ?
              and category is not null
              and category != ''
            ORDER BY category;
        ", ['published', Carbon::now()]))
        ->map(fn($cat) => (object)[
            'code' => Str::slug($cat->category),
            'name' => $cat->category,
        ]);
    }

}
