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
        Schema::create('listing_leads', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bitrix_id')->unsigned()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile')->nullable();


            $table->string('title')->nullable();
            $table->string('bed_id')->nullable();
            $table->string('bath_id')->nullable();
            $table->json('for')->nullable(); // 1: For Sale 2: For Rent 3: For Short Term
            $table->string('unit_no')->nullable();

            $table->bigInteger('type_id')->unsigned()->nullable();
            $table->bigInteger('source_id')->unsigned()->nullable();
            $table->bigInteger('medium_id')->unsigned()->nullable();
            $table->bigInteger('location_id')->unsigned()->nullable();
            $table->bigInteger('property_category_id')->unsigned()->nullable();
            $table->bigInteger('telesales_id')->unsigned()->nullable();

            $table->bigInteger('community_property_id')->unsigned()->nullable();
            $table->foreignId('community_id')->nullable()->constrained('communities');
            $table->foreignId('sub_community_id')->nullable()->constrained('sub_communities');


            $table->string('location_type')->nullable();
            $table->string('recording_file')->nullable();


            $table->text('note')->nullable();

            $table->bigInteger('status_id')->unsigned()->nullable();

            $table->bigInteger('contact_id')->unsigned()->nullable();
            $table->bigInteger('property_id')->unsigned()->nullable();
            $table->bigInteger('manager_id')->unsigned()->nullable();
            $table->bigInteger('sale_id')->unsigned()->nullable()->index();
            $table->bigInteger('created_by_id')->unsigned()->nullable()->index();
            $table->bigInteger('updated_by_id')->unsigned()->nullable()->index();
            $table->bigInteger('deleted_by_id')->unsigned()->nullable()->index();
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
        Schema::dropIfExists('listing_leads');
    }
};
