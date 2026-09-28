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
        Schema::table('leads', function (Blueprint $table) {
            $table->dateTime('in_progress_warning_sent_at')->nullable();
            $table->dateTime('qualified_time')->nullable();
            $table->dateTime('under_offer_time')->nullable();
            $table->dateTime('qualified_warning_sent_at')->nullable();
            $table->dateTime('under_offer_warning_sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn('in_progress_warning_sent_at');
            $table->dropColumn('qualified_time');
            $table->dropColumn('under_offer_time');
            $table->dropColumn('qualified_reminder_sent_at');
            $table->dropColumn('under_offer_warning_sent_at');
        });
    }
};
