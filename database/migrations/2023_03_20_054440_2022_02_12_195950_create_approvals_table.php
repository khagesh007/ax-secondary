<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    public function up()
    {
        Schema::create(table: 'approvals', callback: function (Blueprint $table) {
            $table->id();
            $table->nullableMorphs(config(key: 'approval.approval.approval_pivot'));
            $table->enum('state', ['pending', 'approved', 'rejected'])->default('pending');
            $table->json('new_data')->nullable();
            $table->json('original_data')->nullable();
            $table->foreignId("created_by_id")->nullable()->constrained("users")->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->restrictOnDelete()->restrictOnUpdate();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(table: 'approvals');
    }
};
