<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\BookingStatus; // استيراد الـ Enum الخاص بحالة الحجز

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 
            
            $table->foreignId('event_id')->constrained()->onDelete('cascade'); 
            
            $table->integer('total_tickets'); 
            $table->integer('total_price');
            
            $table->enum('status', array_column(BookingStatus::cases(), 'value'))->default(BookingStatus::PENDING->value); 
 
            
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
