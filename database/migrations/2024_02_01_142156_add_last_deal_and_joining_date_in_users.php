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
        Schema::table('users', function (Blueprint $table) {
            $table->dateTime('joining_date')->nullable();
            $table->dateTime('last_deal_date')->nullable();
            $table->boolean('is_report_exempt')->nullable()->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('joining_date');
            $table->dropColumn('last_deal_date');
            $table->dropColumn('is_report_exempt');
        });
    }
};
