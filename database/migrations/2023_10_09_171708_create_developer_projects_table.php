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
        Schema::create('developer_projects', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->integer('developer_id')->unsigned();
            $table->string('project_name');
            $table->string('rera_permit')->nullable();
            $table->text('description')->nullable();

            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->foreignId('community_id')->nullable()->constrained('communities');
            $table->foreignId('sub_community_id')->nullable()->constrained('sub_communities');
            $table->foreignId('community_property_id')->nullable()->constrained('community_properties');

            $table->boolean('status')->default(1);
            $table->string('icon')->nullable();
            $table->string('badge')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('image')->nullable();
            $table->integer('order')->unsigned()->default(0)->nullable();
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
        Schema::dropIfExists('developer_projects');
    }
};
