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
        Schema::create('public_documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('file_name',300)->nullable();
            $table->string('description')->nullable();
            $table->json('meta')->nullable();
            $table->string('ip_address',45)->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('is_public')->default(false);
            $table->foreignId("created_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('public_documents');
    }
};
