<?php

namespace Postgen\Moderator\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Postgen\Generator\Models\Post;

class Article extends Model
{
    protected $table = 'articles';
    protected $guarded = [];
    protected $casts = [
        'tags' => 'array',
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
