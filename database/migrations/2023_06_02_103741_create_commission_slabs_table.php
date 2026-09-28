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
        Schema::create('commission_slabs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(); // Agent default
            $table->text('description')->nullable();
            $table->enum('frequency',['Daily','Weekly','Monthly','Quarterly','Biannually','Annually', 'Not applicable'])->default("Monthly")->nullable();
            $table->enum('commission_type',['Percentage','Fixed'])->default('Percentage');
            $table->double('commission_value', 10, 2)->default(0)->nullable();
            $table->enum('commission_category',['Direct','Indirect'])->nullable();
            $table->enum('calculation_from', ['Net Commission', 'Remaining Amount'])->nullable();
            $table->string('slug')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->string('badge')->nullable();
            $table->integer('order')->unsigned()->default(100)->nullable();
            $table->boolean('status')->default(1);
            $table->bigInteger('approved_by_id')->unsigned()->nullable()->index();
            $table->bigInteger('created_by_id')->unsigned()->nullable()->index();
            $table->bigInteger('updated_by_id')->unsigned()->nullable()->index();
            $table->foreign('approved_by_id')
                ->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('created_by_id')
                ->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('updated_by_id')
                ->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('commission_slabs');
    }
};
