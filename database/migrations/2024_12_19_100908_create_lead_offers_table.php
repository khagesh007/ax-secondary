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
        Schema::create('lead_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->string('amount')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('active');
            $table->foreignId('created_by_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('updated_by_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('deleted_by_id')->nullable()->constrained('users')->onDelete('cascade');
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
        Schema::dropIfExists('lead_offers');
    }
};
