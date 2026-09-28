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
        Schema::create('deal_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('contact_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('subject',255)->nullable();
            $table->text('description')->nullable();
            $table->date('due_date')->nullable();
            $table->enum('priority',['low','medium','high'])->nullable();
            $table->enum('status', ['PENDING', 'RUNNING', 'COMPLETED', 'CANCELED'])->default('PENDING');
            $table->foreignId("created_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->softDeletes();
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
        Schema::dropIfExists('deal_tasks');
    }
};
