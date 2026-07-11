<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ticket types — dipisah supaya participants (di migration berikutnya)
 * bisa refer FK ke ticket_types.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('ticket_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Early Bird, Regular, VIP
            $table->decimal('price', 12, 2);
            $table->string('currency', 3)->default('IDR');
            $table->integer('quota')->nullable();
            $table->integer('sold_count')->default(0);
            $table->string('description')->nullable();
            $table->dateTime('sale_start')->nullable();
            $table->dateTime('sale_end')->nullable();
            $table->timestamps();
            $table->index('event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_types');
    }
};
