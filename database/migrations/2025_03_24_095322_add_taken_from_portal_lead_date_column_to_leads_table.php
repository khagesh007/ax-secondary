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
            $table->timestamp('portal_lead_transferred_date')->after('portal_lead')->nullable();
            $table->timestamp('transfer_to_portal_lead_time')->after('portal_lead_transferred_date')->nullable();
            $table->timestamp('transfer_to_portal_lead_reminder_sent_at')->after('transfer_to_portal_lead_time')->nullable();
            $table->timestamp('transfer_to_portal_lead_warning_sent_at')->after('transfer_to_portal_lead_reminder_sent_at')->nullable();
            $table->timestamp('taken_from_portal_lead_date')->after('transfer_to_portal_lead_warning_sent_at')->nullable();
            $table->timestamp('in_progress_time')->after('taken_from_portal_lead_date')->nullable();
            $table->timestamp('in_progress_reminder_sent_at')->after('in_progress_time')->nullable();
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
            $table->dropColumn('portal_lead_transferred_date');
            $table->dropColumn('transfer_to_portal_lead_time');
            $table->dropColumn('transfer_to_portal_lead_reminder_sent_at');
            $table->dropColumn('in_progress_time');
            $table->dropColumn('in_progress_reminder_sent_at');
        });
    }
};
