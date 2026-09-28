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
        Schema::create('offplan_properties', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->nullable();
            $table->integer('property_id')->unsigned()->nullable();
            $table->integer('developer_id')->unsigned()->nullable()->default(1);
            $table->string('unit_no')->nullable();

            $table->enum('completion_status', ['Ready Primary', 'Ready Secondary', 'Off-Plan Primary', 'Off-Plan Secondary'])->nullable()->default('Off-Plan Primary');

            $table->string('name')->nullable();
            $table->string('title')->nullable(); // need to generate this
            $table->text('description')->nullable();
            $table->string('address_details')->nullable();

            $table->string('bath_id')->nullable();
            $table->string('bed_id')->nullable();

            $table->string('project_name')->nullable();
            $table->string('building_location')->nullable();
            $table->string('building_name')->nullable();
            $table->string('community_name')->nullable();
            $table->string('property_name')->nullable(); // building name



            $table->string('location_code')->nullable();
            $table->string('location_id')->nullable();

            $table->string('parking')->nullable();
            $table->integer('plot_size')->nullable();
            $table->integer('total_area')->nullable();



            $table->string('property_type')->nullable(); // Apartment or Villa
            $table->string('property_usage')->nullable();  // residential or commercial

            $table->string('selling_price')->nullable();
            $table->string('price')->nullable();
            $table->string('land_registration_fee')->nullable();
            $table->string('applicable_fee')->nullable();
            $table->string('total_amount')->nullable();





            $table->boolean('recently_added')->nullable()->default(true);
            $table->boolean('will_delete')->nullable()->default(true);

            $table->json('data')->nullable();
            $table->json('location_data')->nullable();
            $table->timestamp('emaar_created_at')->nullable();

            $table->string('status')->nullable();
            $table->string('booking_status')->nullable();

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
        Schema::dropIfExists('offplan_properties');
    }
};
