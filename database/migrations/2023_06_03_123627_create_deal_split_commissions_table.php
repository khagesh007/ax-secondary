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
        Schema::create('deal_split_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId("deal_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId("deal_split_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId("user_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->double("commission")->nullable()->default(0); // This is the commission percentage or fixed amount
            $table->double("commission_value")->nullable()->default(0);
            $table->enum("commission_type",["Percentage", "Fixed"])->nullable()->default("Percentage");
            $table->enum('commission_category', ['DIRECT', 'INDIRECT'])->default('DIRECT');
            $table->enum('commission_from',['Net Commission','Remaining Amount'])->default('Net Commission');
            $table->text("description")->nullable();
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
        Schema::dropIfExists('deal_split_commissions');
    }
};
