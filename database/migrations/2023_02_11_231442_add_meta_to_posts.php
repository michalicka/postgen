<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->string('category')->nullable()->after('user_id');
            $table->text('tags')->default('[]')->after('slug');
            $table->string('image')->nullable()->after('dislikes');
            $table->timestamp('published_at')->nullable()->after('updated_at');
        });

        \DB::update("
            UPDATE posts
            SET published_at = updated_at
            WHERE status = ?
        ", [ 'published' ]);
    }

    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->dropColumn('tags');
            $table->dropColumn('image');
            $table->dropColumn('published_at');
        });
    }
};
