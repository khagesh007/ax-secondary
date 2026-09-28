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
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('event_type_id')->after('id')->nullable()->constrained('event_types')->onDelete('cascade');
            $table->timestamp('last_reminded')->after('reminder_frequency')->nullable();
            $table->renameColumn('email_to_attendees', 'send_notification');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['event_type_id']);
            $table->dropColumn(['last_reminded']);
            $table->renameColumn( 'send_notification','email_to_attendees');
        });
    }
};
