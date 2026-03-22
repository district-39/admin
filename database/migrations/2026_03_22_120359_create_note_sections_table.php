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
        Schema::create('note_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_meeting_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('committee')->nullable();
            $table->integer('order');
            $table->json('json')->nullable();
            $table->text('text')->nullable();
            $table->text('markdown')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note_sections');
    }
};
