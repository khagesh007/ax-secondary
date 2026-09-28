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
        Schema::table('temp_properties', function (Blueprint $table) {
            if (!Schema::hasColumn('temp_properties', 'buyer_type')) {
                $table->string('buyer_type')->nullable()->after('description');
            }
            if (!Schema::hasColumn('temp_properties', 'buyer_timeline')) {
                $table->string('buyer_timeline')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('temp_properties', function (Blueprint $table) {
            if (Schema::hasColumn('temp_properties', 'buyer_type')) {
                $table->dropColumn('buyer_type');
            }
            if (Schema::hasColumn('temp_properties', 'buyer_timeline')) {
                $table->dropColumn('buyer_timeline');
            }
        });
    }
};
