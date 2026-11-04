<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\TicketType;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_ticket_types', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('event_id')->constrained()->onDelete('cascade'); 
            $table->enum('type', array_column(TicketType::cases(), 'value'))->default(TicketType::NORMAL->value); 
            $table->integer('price');
            
            $table->integer('total_quantity');
            $table->unique(['event_id', 'type']);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_ticket_types');
    }
};
