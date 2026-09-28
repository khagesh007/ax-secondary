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
        Schema::create('leads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('my_crm_id')->nullable();
            $table->enum('title', ['Mr.', 'Mrs.', 'Ms.', 'Miss.', 'Dr.', 'Prof.'])->nullable();
            $table->bigInteger('property_id')->unsigned()->nullable();
            $table->bigInteger('mail_number')->unsigned()->unique()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('address')->nullable();
            $table->string('company')->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Unknown'])->nullable();
            $table->enum('priority', ['Urgent', 'High', 'Normal', 'Low'])->nullable();
            $table->bigInteger('nationality_id')->unsigned()->nullable();
            $table->bigInteger('country_id')->unsigned()->nullable();
            $table->bigInteger('state_id')->unsigned()->nullable();
            $table->bigInteger('city_id')->unsigned()->nullable();
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->bigInteger('lead_status_id')->unsigned()->nullable();
            $table->bigInteger('lead_sub_status_id')->unsigned()->nullable();
            $table->bigInteger('lead_type_id')->unsigned()->nullable();
            $table->bigInteger('lead_source_id')->unsigned()->nullable();

            $table->bigInteger('lead_medium_id')->unsigned()->nullable(); // if we want to revert changes to old tables then we have to delete this column

            $table->bigInteger('type_id')->unsigned()->nullable(); // if we want to revert changes to old tables then we have to delete this column
            $table->bigInteger('source_id')->unsigned()->nullable(); // if we want to revert changes to old tables then we have to delete this column

            $table->bigInteger('agent_id')->unsigned()->nullable();
            $table->bigInteger('previous_agent_id')->unsigned()->nullable()->index();
            $table->bigInteger('telesales_id')->unsigned()->nullable();
            $table->bigInteger('telesales_head_id')->unsigned()->nullable();
            $table->dateTime('lead_assignment_date')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->date('viewing_date')->nullable();

            $table->bigInteger('created_by_id')->unsigned()->nullable()->index();
            $table->bigInteger('updated_by_id')->unsigned()->nullable()->index();
            $table->bigInteger('assigned_by_id')->unsigned()->nullable()->index();
            $table->dateTime('assigned_at')->nullable();

            $table->dateTime('assigned_agent_at')->nullable();

            $table->json('for')->nullable(); // 1: For Sale 2: For Rent 3: For Short Term
            $table->boolean('status')->default(1);
            $table->boolean('warning_sent')->default(0);
            $table->bigInteger('converted_to_listing_lead')->unsigned()->nullable();
            $table->text('subject')->nullable();
            $table->text('message')->nullable();
            $table->text('note')->nullable();
            $table->string('pf_tracking_link', 500)->nullable();
            $table->boolean('is_archive')->nullable()->default(false);
            $table->foreign('lead_status_id')
            ->references('id')->on('lead_statuses')
            ->onDelete('restrict')->onUpdate('restrict');

            $table->foreign('lead_sub_status_id')
            ->references('id')->on('lead_sub_statuses')
            ->onDelete('restrict')->onUpdate('restrict');

            $table->foreign('lead_source_id')
            ->references('id')->on('lead_sources')
            ->onDelete('restrict')->onUpdate('restrict');


            $table->foreign('lead_type_id')
            ->references('id')->on('lead_types')
            ->onDelete('restrict')->onUpdate('restrict');

// if we want to revert changes to old tables then we have to delete this column
            $table->foreign('source_id')
            ->references('id')->on('sources')
            ->onDelete('restrict')->onUpdate('restrict');

            $table->foreign('type_id')
            ->references('id')->on('types')
            ->onDelete('restrict')->onUpdate('restrict');
// if we want to revert changes to old tables then we have to delete this column


            $table->foreign('agent_id')
                ->references('id')->on('users')
                ->onDelete('restrict')->onUpdate('restrict');

                $table->foreign('telesales_id')
                ->references('id')->on('users')
                ->onDelete('restrict')->onUpdate('restrict');

                $table->foreign('telesales_head_id')
                ->references('id')->on('users')
                ->onDelete('restrict')->onUpdate('restrict');


            $table->foreign('created_by_id')
                ->references('id')->on('users')
                ->onDelete('restrict')->onUpdate('restrict');


            $table->foreign('updated_by_id')
                ->references('id')->on('users')
                ->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('assigned_by_id')
                ->references('id')->on('users')
                ->onDelete('restrict')->onUpdate('restrict');



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
        Schema::dropIfExists('leads');
    }
};
