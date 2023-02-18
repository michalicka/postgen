<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('site_id');
            $table->unsignedBigInteger('external_id')->nullable();
            $table->string('category')->nullable();
            $table->text('title');
            $table->longtext('content');
            $table->text('tags')->default('[]');
            $table->string('image')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('published_at')->nullable();

            $table->foreign('user_id')->on('users')->references('id');
            $table->foreign('post_id')->on('posts')->references('id');
            $table->foreign('site_id')->on('sites')->references('id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
};
