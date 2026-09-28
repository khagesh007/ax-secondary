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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId("deal_type_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            // $table->foreignId("transaction_type_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId("property_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            // $table->foreignId("lead_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId("contact_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            // $table->foreignId("source_id")->nullable()->constrained()->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId("deal_status_id")->nullable();
            // $table->foreignId("deal_stage_id")->nullable();
            $table->string('status_message')->nullable();
            $table->string('state')->default('INITIAL');
            $table->string('type')->nullable();
            $table->string('transaction_type')->nullable();
            $table->enum('property_type',['Secondary','Off plan'])->default('Secondary');
            $table->bigInteger("deal_price")->nullable()->default(0);
            $table->bigInteger("initial_deposit")->nullable()->default(0);
            $table->double("net_commission", 20, 2)->nullable()->default(0);
            $table->double("vat", 20, 2)->nullable()->default(0);
            $table->double("gross_commission", 20, 2)->nullable()->default(0);
            $table->string('status',200)->default('DRAFT');
            $table->bigInteger('agent_id')->unsigned()->nullable()->index();
            $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId("created_by_id")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId("updated_by_id")->nullable()->constrained("users")->nullOnDelete()->cascadeOnUpdate();
            $table->json('document_notes')->nullable();
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
        Schema::dropIfExists('deals');
    }
};
