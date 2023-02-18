<?php

namespace Postgen\Moderator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;
use Postgen\Generator\Models\Post;

class Article extends Model
{
    use SoftDeletes;

    protected $table = 'articles';
    protected $guarded = [];
    protected $casts = [
        'tags' => 'array',
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
