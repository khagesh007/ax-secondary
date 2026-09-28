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
        Schema::create('traccar_client_settings', function (Blueprint $table) {
            $table->id();
            $table->string('server_url')->nullable();
            $table->string('username',50)->nullable();
            $table->string('password',50)->nullable();
            $table->string('port')->nullable();
            $table->enum('location_accuracy', ['High', 'Medium', 'Low'])->default('Medium');
            $table->bigInteger('coordinates_accuracy')->unsigned()->nullable();
            $table->decimal('version', 5, 2)->nullable()->default(0);
            $table->string('frequency')->nullable();
            $table->string('distance')->nullable();
            $table->string('angle')->nullable();
            $table->boolean('offline_buffering')->default(1);
            $table->time('track_from')->nullable();
            $table->time('track_to')->nullable();
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
        Schema::dropIfExists('traccar_client_settings');
    }
};
