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
        Schema::create('deal_progression_data', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_type')->nullable();
            $table->string('conveyancer_selection_mode')->nullable(); // AUTO, MANUAL
            $table->foreignId("deal_id")->constrained("deals")->cascadeOnUpdate();
            $table->foreignId('conveyancer_id')->constrained("users")->cascadeOnUpdate();
            $table->double("progression_amount", 20, 2)->nullable()->default(0);
            $table->datetime('deal_completed_at')->nullable();
            $table->datetime('amount_received_at')->nullable();
            $table->text('remarks')->nullable();
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
        Schema::dropIfExists('deal_progression_data');
    }
};
