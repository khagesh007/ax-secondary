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
        Schema::create('commission_slab_limits', function (Blueprint $table) {
            $table->id();
            $table->double('from_value')->unsigned()->default(0)->nullable();
            $table->double('to_value')->default(0)->nullable();
            $table->double('commission_value', 10, 2)->default(0)->nullable();
            $table->enum('commission_type',['Percentage','Fixed'])->default('Percentage');
            $table->bigInteger('commission_slab_id')->unsigned()->nullable()->index();
            $table->foreign('commission_slab_id')->references('id')->on('commission_slabs')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('created_by_id')->unsigned()->nullable()->index();
            $table->bigInteger('updated_by_id')->unsigned()->nullable()->index();
            $table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('updated_by_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('commission_slab_limits');
    }
};
