<?php

namespace Postgen\Common\Logic;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class Links
{
    public static function list(): Collection
    {
        return collect(\DB::select("
            SELECT content
            FROM posts
            WHERE status = ?
              AND published_at <= ?
              and category is not null
              and category != ''
              and user_id = ?
              and content like ?
            ORDER BY category;
        ", ['published', Carbon::now(), auth()->id(), '%://%']))
        ->map(function($post) {
            return collect(preg_match_all('/href="([^\"]+)"[^>]+>([^<]+)/', $post->content, $matches, PREG_SET_ORDER) ? $matches : [])
                ->map(fn($match) => [
                    'code' => trim($match[1]),
                    'name' => trim($match[2]),
                ]);
        })
        ->flatten(1)
        ->groupBy('code')
        ->map(fn($group, $link) => [
            'code' => $link,
            'name' => $group->pluck('name')->unique()->values(),
            'count' => $group->count(),
        ])
        ->sortBy([['count', 'desc']])
        ->values();
    }

}
