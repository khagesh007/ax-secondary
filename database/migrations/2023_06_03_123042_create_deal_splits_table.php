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
        Schema::create('deal_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId("deal_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId("user_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->double("split_amount",10,2)->nullable()->default(0);
            $table->string('split_percentage',30)->nullable();
            $table->string('commission_source')->nullable();
            $table->text("description")->nullable();
            $table->foreignId("approved_by_id")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId("created_by_id")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
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
        Schema::dropIfExists('deal_splits');
    }
};
