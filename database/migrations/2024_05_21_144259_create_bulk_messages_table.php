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
        Schema::create('bulk_messages', function (Blueprint $table) {
            $table->id();
            $table->string('short_url')->unique();
            $table->string('custom_link')->nullable();
            $table->string('batch_no');
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->string('full_name');
            $table->string('mobile');
            $table->string('message');
            $table->string('type')->default('sms');
            $table->tinyInteger('status')->default(2);
            $table->boolean('confirmed')->nullable()->default(false);
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->json('response')->nullable();
            $table->timestamp('link_opened_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bulk_messages');
    }
};
