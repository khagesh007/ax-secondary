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
        Schema::create('deal_deductible_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->enum('type',['percentage','fixed'])->default('percentage');
            $table->double('type_value', 8, 2)->default(0);

            $table->integer('order')->unsigned()->default(1)->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('image')->nullable();
            $table->string('icon')->nullable();
            $table->string('badge')->nullable();
            $table->boolean('status')->default(1);
            
            $table->bigInteger('created_by_id')->unsigned()->nullable()->index();
            $table->bigInteger('updated_by_id')->unsigned()->nullable()->index();
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
        Schema::dropIfExists('deal_deductible_types');
    }
};
