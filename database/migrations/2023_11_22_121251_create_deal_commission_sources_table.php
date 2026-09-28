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
        Schema::create('deal_commission_sources', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('deal_id')->nullable()->unsigned();
            $table->bigInteger('commission_amount')->nullable();
            $table->double('commission_percentage',5,2)->nullable();
            $table->string('commission_source')->nullable();
            $table->boolean('is_vat_applicable')->nullable()->default(true);
            $table->bigInteger('vat_amount')->nullable();
            $table->bigInteger('vat_percentage')->nullable();
            $table->double('sales_progression_amount',10,2)->nullable();
            $table->bigInteger('created_by_id')->nullable()->unsigned();
            $table->bigInteger('updated_by_id')->nullable()->unsigned();
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
        Schema::dropIfExists('deal_commission_sources');
    }
};
