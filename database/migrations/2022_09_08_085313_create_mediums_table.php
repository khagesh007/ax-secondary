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
        Schema::create('mediums', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('status')->default(1);
            $table->boolean('is_contact_medium')->default(1);
            $table->boolean('is_lead_medium')->default(1);
            
            $table->string('icon')->nullable();
            $table->string('badge')->nullable();
            $table->string('image')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->string("thumbnail")->nullable();

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
        Schema::dropIfExists('mediums');
    }
};
