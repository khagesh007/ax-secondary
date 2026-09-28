<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\LeadSource;
use App\Models\Source;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->boolean('status')->default(1);
            
            $table->unsignedInteger('order')->default(0);
            $table->string("thumbnail")->nullable();
            $table->string('icon')->nullable();
            $table->string('badge')->nullable();
            $table->string('image')->nullable();

            $table->boolean('is_contact_source')->default(1);
            $table->boolean('is_lead_source')->default(1);
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


        
        $sources = LeadSource::all();
        foreach($sources as $source) {

            Source::create([
                'name' => $source->name,
                'status' => $source->status,
                'created_by_id' => $source->created_by_id,
                'updated_by_id' => $source->updated_by_id,
                'created_at' => $source->created_at,
                'updated_at' => $source->updated_at,
                'deleted_at' => $source->deleted_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sources');
    }
};
