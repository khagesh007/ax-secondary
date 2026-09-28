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
        Schema::create('deal_deductibles', function (Blueprint $table) {
            $table->id();
            $table->foreignId("deal_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('deal_deductible_type_id')->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->string('deductible_type')->nullable();
            $table->enum('amount_type',['Fixed','Percentage'])->default('Percentage');
            $table->double('amount',10,2)->default(0);
            $table->string('description')->nullable();
            $table->boolean('status')->nullable()->default(true);
            $table->foreignId("created_by_id")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('deal_deductibles');
    }
};
