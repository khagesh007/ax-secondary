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
        Schema::create('republish_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable(); // agent_id
            $table->unsignedBigInteger('property_id')->nullable();
            $table->unsignedBigInteger('admin_id')->nullable();
            $table->string('file')->nullable();
            $table->string('password')->nullable();
            $table->string('old_ref_no')->nullable();
            $table->string('new_ref_no')->nullable();
            $table->string('status')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('republish_requests');
    }
};
