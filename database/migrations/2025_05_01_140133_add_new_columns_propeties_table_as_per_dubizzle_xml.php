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
            //fields added as per dubizzle xml
            $table->string('offplan_dld_wavier')->nullable();
            $table->string('offplan_original_price')->nullable();
            $table->string('offplan_amount_paid')->nullable();
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
            $table->dropColumn('offplan_dld_wavier');
            $table->dropColumn('offplan_original_price');
            $table->dropColumn('offplan_amount_paid');
        });
    }
};
