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
        Schema::create('deal_split_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained()->onDelete('cascade');
            $table->foreignId('deal_split_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->double('referral_amount')->nullable()->default(0); // This is the referral percentage or fixed amount
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('deal_split_referrals');
    }
};
