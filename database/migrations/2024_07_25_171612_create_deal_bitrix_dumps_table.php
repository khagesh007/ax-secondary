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
        Schema::create('deal_bitrix_dumps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->nullable()->constrained('users');
            $table->string('bitrix_deal_id')->nullable();
            $table->string('bitrix_lead_id')->nullable();
            $table->foreignId('deal_id')->nullable()->constrained('deals');
            $table->foreignId('lead_id')->nullable()->constrained('leads');
            $table->json('deal_data')->nullable();
            $table->json('deal_leads')->nullable();
            $table->string('status',150)->nullable();
            $table->boolean('is_used')->default(false);
            $table->boolean('lead_source_matched')->default(false);
            $table->string('deal_created_at')->nullable();
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
        Schema::dropIfExists('deal_bitrix_dumps');
    }
};
