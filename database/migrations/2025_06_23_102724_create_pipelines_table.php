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
        Schema::create('pipelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('deal_type')->nullable();
            $table->string('property_details')->nullable();
            $table->foreignId('property_id')->nullable()->constrained('properties')->onDelete('set null');
            $table->string('deal_value')->nullable();
            $table->string('commission_type')->nullable();
            $table->string('commission_value')->nullable();
            $table->text('comment')->nullable();
            $table->string('priority')->default('Low'); // Low, Medium, High
            $table->date('closing_date')->nullable();
            $table->string('status')->default('Active'); //Active, Lost, Won
            $table->foreignId('source_id')->nullable()->constrained('sources')->onDelete('set null');
            $table->text('manager_comment')->nullable();
            $table->text('reason_for_loss')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('deleted_by')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes(); // For soft delete functionality
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
        Schema::dropIfExists('pipelines');
    }
};
