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
        Schema::create('lead_criterias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->decimal('min_price', 20, 0)->nullable();
            $table->decimal('max_price', 20, 0)->nullable();
            $table->bigInteger('min_bath_id')->unsigned()->nullable()->index();
            $table->foreign('min_bath_id')->references('id')
            ->on('baths')->onDelete('cascade')->onUpdate('cascade');

            $table->bigInteger('max_bath_id')->unsigned()->nullable()->index();
            $table->foreign('max_bath_id')->references('id')
            ->on('baths')->onDelete('cascade')->onUpdate('cascade');

            $table->bigInteger('min_bed_id')->unsigned()->nullable()->index();
            $table->foreign('min_bed_id')->references('id')->on('beds')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->bigInteger('max_bed_id')->unsigned()->nullable()->index();
            $table->foreign('max_bed_id')->references('id')->on('beds')
            ->onDelete('cascade')->onUpdate('cascade');

            $table->json('communities')->nullable();
            $table->string('furnished')->nullable();
            $table->string('view')->nullable();

            $table->foreignId("created_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
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
        Schema::dropIfExists('lead_criterias');
    }
};
