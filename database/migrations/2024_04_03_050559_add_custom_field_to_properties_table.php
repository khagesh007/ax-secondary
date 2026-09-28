<?php
ini_set('memory_limit', -1);
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
        Schema::table('properties', function (Blueprint $table) {
            $table->timestamp('republished_date')->nullable();
        });
        $properties = App\Models\Property::all();
        foreach($properties as $property){
            $firstPublished = $property->firstpublish()->first()?->created_at?->toDateTimestring() ?? null;
            $property->published_date = $firstPublished;
            $property
            ->withoutApproval()
            ->save();
        }
        foreach($properties as $property){
            $firstPublished = $property->firstunpublish()->first()?->created_at?->toDateTimestring() ?? null;
            $property->unpublished_date = $firstPublished;
            $property ->withoutApproval()
            ->save();
        }
        foreach($properties as $property){
            $firstPublished = $property->lastpublish()->first()?->created_at?->toDateTimestring() ?? null;
            $property->republished_date = $firstPublished;
            $property->withoutApproval()
            ->save();
        }



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn('republished_date');
        });
    }
};
