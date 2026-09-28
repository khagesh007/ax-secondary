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
        Schema::create('deal_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId("deal_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('lead_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->enum('lead_category', ['COMPANY', 'PERSONAL','REFERRAL'])->default('PERSONAL');
            $table->string('lead_type')->nullable();
            $table->foreignId("created_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('deal_leads');
    }
};
