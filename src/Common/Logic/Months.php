<?php

namespace Postgen\Common\Logic;

use Carbon\Carbon;

class Months
{
    public static function list(): array
    {
        return \DB::select("
            SELECT DISTINCT
                DATE_FORMAT(published_at, '%M') AS name,
                DATE_FORMAT(published_at, '%m') AS month,
                DATE_FORMAT(published_at, '%Y') AS year
            FROM posts
            WHERE status = ?
              AND published_at <= ?
            ORDER BY published_at DESC;
        ", ['published', Carbon::now()]);
    }
}
