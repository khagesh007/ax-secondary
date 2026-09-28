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
        Schema::create('temp_properties', function (Blueprint $table) {
            $table->id();
            $table->string('my_crm_id')->nullable();
            $table->string('ref_no')->nullable()->index();
            $table->string('title',255)->nullable();
            $table->text('description')->nullable();
            $table->string('listing_source')->nullable();
            $table->enum('for', ['Residential for sale', 'Residential for rent', 'Commercial for sale', 'Commercial for rent'])->nullable();
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->foreignId('community_id')->nullable()->constrained('communities');
            $table->foreignId('sub_community_id')->nullable()->constrained('sub_communities');
            $table->foreignId('community_property_id')->nullable()->constrained('community_properties');
            $table->foreignId('property_category_id')->nullable()->constrained('property_categories');
            $table->foreignId('bed_id')->nullable()->constrained('beds');
            $table->foreignId('bath_id')->nullable()->constrained('baths');
            $table->string('unit_no')->nullable();
            $table->string('built_up_area')->nullable();
            $table->string('plot_size')->nullable();
            $table->string('street')->nullable();
            $table->integer('floor')->nullable();
            $table->text('view')->nullable();
            $table->text('villa_type')->nullable();
            $table->integer('parking')->nullable();
            $table->string('dewa_number')->nullable();
            $table->string('rera_permit_number')->nullable();
            $table->date('rera_issue_date')->nullable();
            $table->date('rera_expiry_date')->nullable();
            $table->foreignId('property_type_id')->nullable()->constrained('property_types');
            $table->foreignId('property_status_id')->nullable()->constrained('property_statuses');
            $table->enum('completion_status', ['Ready Primary', 'Ready Secondary','Off-Plan Primary','Off-Plan Secondary'])->nullable();
            $table->decimal("rented_at", 15, 2)->nullable();
            $table->date("rented_until")->nullable();
            $table->date("next_available")->nullable();
            $table->date("availability_date")->nullable();
            $table->unsignedTinyInteger("remind")->nullable();
            $table->string('furnished')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('exclusive')->default(false);
            $table->date('exclusive_start')->nullable();
            $table->date('exclusive_end')->nullable();
            $table->boolean('hot')->default(false);
            $table->enum('publish_status', ['PUBLISHED', 'POCKET', 'UNPUBLISHED','DRAFT', 'PENDING', 'ARCHIVED', 'REFUSED'])->default('DRAFT');
            $table->enum('state', ['PENDING', 'APPROVED', 'REFUSED'])->nullable();
            $table->boolean('is_live')->default(false);
            $table->boolean('offplan_portal_fetch')->default(false);
            $table->string('request_status_type')->nullable();
            $table->bigInteger('agent_id')->unsigned()->nullable()->index();
            $table->bigInteger('previous_agent_id')->unsigned()->nullable()->index();
            $table->bigInteger('telesales_id')->unsigned()->nullable()->index();
            $table->foreign('agent_id')
            ->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('owner_id')->unsigned()->nullable()->index();
            $table->foreign('owner_id')
            ->references('id')->on('contacts')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string("cheques")->nullable();
            $table->boolean('mycrm_updated')->default(0);
            $table->bigInteger('created_by_id')->unsigned()->nullable()->index();
            $table->bigInteger('updated_by_id')->unsigned()->nullable()->index();
            $table->foreign('created_by_id')
            ->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');

            $table->foreign('updated_by_id')
            ->references('id')->on('users')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->string("video_link",255)->nullable();
            $table->string("video_360_link",255)->nullable();
            $table->string("qr_link",255)->nullable();
            $table->string("title_deed_text",255)->nullable();
            $table->integer('data_version')->default(1);
            $table->timestamp('published_date')->nullable();
            $table->timestamp('unpublished_date')->nullable();
            $table->date("contract_start")->nullable();
            $table->date("contract_end")->nullable();
            $table->enum('payment_method', ['cash', 'installments', 'cash,installments'])->nullable();
            $table->decimal("down_payment_price", 15, 0)->nullable();
            $table->string("build_year")->nullable();
            $table->bigInteger('deleted_by_id')->unsigned()->nullable()->index();
            $table->foreign('deleted_by_id')
                ->references('id')->on('users')
                ->onDelete('restrict')->onUpdate('restrict');
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
        Schema::dropIfExists('temp_properties');
    }
};
