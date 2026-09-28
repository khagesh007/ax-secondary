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
        Schema::table('properties', function (Blueprint $table) {
            $table->boolean('need_to_archive')->default(false)->after('is_archived');
            $table->unsignedBigInteger('by_property_id')->nullable()->after('need_to_archive');
            $table->unsignedBigInteger('duplicate_score')->nullable()->after('by_property_id');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('need_to_archive');
            $table->dropColumn('by_property_id');
            $table->dropColumn('duplicate_score');
        });
    }
};
