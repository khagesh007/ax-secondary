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
            $table->boolean('is_duplicated')->default(false)->after('is_live');
            $table->string('duplicated_from')->nullable()->after('is_duplicated');
            $table->string('duplicate_reason')->nullable()->after('duplicated_from');
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
            $table->dropColumn('is_duplicated');
            $table->dropColumn('duplicated_from');
            $table->dropColumn('duplicate_reason');
        });
    }
};
