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
        Schema::table('meetings', function (Blueprint $table) {
            $table->dropForeign('meetings_contact_follow_up_id_foreign');
            $table->foreign('contact_follow_up_id')
                ->references('id')
                ->on('contact_follow_ups')
                ->onUpdate('cascade')
                ->onDelete('cascade')
                ->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meetings', function (Blueprint $table) {
            //
        });
    }
};
