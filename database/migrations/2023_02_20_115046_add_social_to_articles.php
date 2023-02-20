<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('facebook', 1024)->nullable()->after('link');
            $table->string('twitter', 1024)->nullable()->after('link');
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('twitter');
            $table->dropColumn('facebook');
        });
    }
};
