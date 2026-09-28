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
        Schema::create('listing_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('property_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('portal_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('submission_id')->nullable();
            $table->string('portal_listing_id')->nullable();
            $table->string('portal_listing_ref_no')->nullable();
            $table->string('public_profile_id')->nullable();
            $table->string('status')->nullable();
            $table->text('remarks')->nullable();
            $table->json('meta')->nullable();
            $table->dateTime('portal_created_at')->nullable();
            $table->dateTime('portal_expires_at')->nullable();
            $table->dateTime('portal_updated_at')->nullable();
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
        Schema::dropIfExists('listing_verifications');
    }
};
