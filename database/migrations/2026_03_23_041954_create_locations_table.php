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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_in_person')->default(true);
            $table->boolean('is_online')->default(false);
            $table->boolean('is_hybrid')->default(true);
            $table->boolean('is_mailing')->default(false);
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('address_ln1')->nullable();
            $table->string('address_ln2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('url')->nullable();
            $table->string('meeting_id')->nullable();
            $table->string('password')->nullable();
            $table->text('location_directions')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->string('region')->nullable();
            $table->string('timezone')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
