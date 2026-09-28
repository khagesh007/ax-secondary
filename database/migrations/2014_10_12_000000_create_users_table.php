<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('bitrix_role')->nullable();
            $table->foreignId("parent_id")->nullable()->constrained("users")->cascadeOnUpdate()->nullOnDelete();
            $table->string('old_user_id')->nullable();
            $table->string('slug')->nullable();
            $table->enum('title', ['Mr.', 'Mrs.', 'Ms.', 'Miss.', 'Dr.', 'Prof.'])->nullable();
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('position')->nullable();
            $table->string('email')->unique();
            $table->string('secondary_email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('mobile')->nullable();
            $table->string('password');
            $table->boolean("api_access")->default(false);
            $table->bigInteger("api_rate_limit")->default(0);
            $table->string('telegram_username',255)->nullable();
            $table->string('telegram_chatid',255)->nullable();
            $table->string('call_tracking_user_id',255)->nullable();
            $table->string('call_tracking_account_id',255)->nullable();
             $table->bigInteger('bitrix_id')->nullable();
            $table->string('website_link')->nullable();
            $table->string('facebook_link')->nullable();
            $table->string('instagram_link')->nullable();
            $table->string('youtube_link')->nullable();
            $table->string('linkedin_link')->nullable();
            $table->text('bio')->nullable();
            $table->string('license_no')->nullable();
            $table->boolean('pp_enabled')->nullable();
            $table->string('pp_name')->nullable();
            $table->string('pp_photo')->nullable();
            $table->string('pp_license_no')->nullable();
            $table->bigInteger("country_id")->nullable();//This will be linked to country table later
            $table->bigInteger("state_id")->nullable();//This will be linked to state table later
            $table->bigInteger("nationality_id")->nullable();//This will be linked to country table later
            $table->bigInteger('community_id')->unsigned()->nullable()->index();
            $table->foreignId('region_id')->nullable()->constrained('regions');
            $table->string("thumbnail")->nullable();
            $table->string("image")->nullable();
            $table->text('avatar')->nullable();
            $table->string("color_code")->nullable();
            $table->boolean("status")->default(true);
            $table->boolean("traccar_status")->default(false);
            $table->string("device_identifier",60)->default(false);
            $table->string("port",50)->default(false);
            $table->boolean("mic_status")->default(false);
            $table->boolean("call_tracking")->default(true);
            $table->boolean("call_lead")->default(false);
            $table->boolean("is_commercial")->default(false);
            $table->foreignId("created_by_id")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
