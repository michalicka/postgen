<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Postgen\Generator\Models\Post;

class Retest extends Command
{
    protected $signature = 'retest';
    protected $description = 'Retest';

    public function handle()
    {
        $post = Post::find(8);
    }
}
