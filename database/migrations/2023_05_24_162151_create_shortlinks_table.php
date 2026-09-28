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
        Schema::create('shortlinks', function (Blueprint $table) {
            $table->id();
            $table->integer('source_id')->unsigned()->nullable();
            $table->string('slug')->nullable();
            $table->string('link', 500)->nullable();
            $table->text('message')->nullable();
            $table->string('short_link')->nullable();
            $table->integer('count')->unsigned()->default(0);
            $table->timestamp('opted_in')->nullable();
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
        Schema::dropIfExists('shortlinks');
    }
};
