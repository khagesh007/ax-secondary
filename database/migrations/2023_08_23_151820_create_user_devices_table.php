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
        Schema::create('user_devices', function (Blueprint $table) {
            $table->id();
            $table->string('traccar_device_id')->nullable();
            $table->foreignId('user_id')->index()->nullable()->constrained('users');
            $table->string('device_name')->index()->nullable();
            $table->string('device_model')->nullable();
            $table->string('device_os')->nullable();
            $table->string('device_os_version')->nullable();
            $table->string('device_token')->nullable();
            $table->string('login_ip')->nullable();
            $table->string('coordinates')->nullable();
            $table->dateTime('last_active_at')->nullable();
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
        Schema::dropIfExists('user_devices');
    }
};
