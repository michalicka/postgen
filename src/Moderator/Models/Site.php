<?php

namespace Postgen\Moderator\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Site extends Model
{
    protected $table = 'sites';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
