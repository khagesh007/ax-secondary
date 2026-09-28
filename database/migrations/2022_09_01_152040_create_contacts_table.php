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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('my_crm_id')->nullable();
            $table->enum('title', ['Mr.', 'Mrs.', 'Ms.', 'Miss.', 'Dr.', 'Prof.'])->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->bigInteger('agent_id')->unsigned()->nullable()->index();
            $table->bigInteger('previous_agent_id')->unsigned()->nullable()->index();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('address', 255)->nullable();
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->foreignId('country_id')->nullable()->constrained();
            $table->bigInteger('nationality_id')->unsigned()->nullable()->index();
            $table->string('passport')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('phone')->nullable();
            $table->string('company', 255)->nullable();
            $table->string('website', 255)->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->foreignId('contact_type_id')->nullable()->constrained();
            $table->foreignId('contact_source_id')->nullable()->constrained();
            $table->foreignId('contact_status_id')->nullable()->constrained();
            $table->bigInteger('created_by_id')->unsigned()->nullable()->index();
            $table->bigInteger('updated_by_id')->unsigned()->nullable()->index();
            $table->foreign('created_by_id')
                ->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('updated_by_id')
                ->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('agent_id')
                ->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('nationality_id')
                ->references('id')->on('countries')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('deleted_by_id')->unsigned()->nullable()->index();
            $table->foreign('deleted_by_id')
                ->references('id')->on('users')
                ->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('contacts');
    }
};
