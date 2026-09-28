<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('temp_bayut_dumps', function (Blueprint $table) {
            $table->boolean('is_push_story_api')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_bayut_dumps', function (Blueprint $table) {
            $table->dropColumn('is_push_story_api');
        });
    }
};
