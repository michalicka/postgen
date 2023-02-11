<?php

namespace Postgen\Generator\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class Post extends Model
{
    use SoftDeletes;

    protected $table = 'posts';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
