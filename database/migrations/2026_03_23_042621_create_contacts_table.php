<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_individual_meeting')->default(false);
            $table->boolean('is_group_contact')->default(false);
            $table->boolean('is_user')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('website')->nullable();
            $table->string('website2')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mailing_address')->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations');
            $table->string('venmo')->nullable();
            $table->string('cashapp')->nullable();
            $table->string('paypal')->nullable();
            $table->string('homegroup_online_code')->nullable();
            $table->date('last_contact')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
