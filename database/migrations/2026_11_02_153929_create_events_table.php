<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\EventStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('venue_id')->constrained()->onDelete('cascade'); 
            $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
            $table->string('title'); 
            
            $table->enum('status', array_column(EventStatus::cases(), 'value'))->default(EventStatus::DRAFT->value); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
