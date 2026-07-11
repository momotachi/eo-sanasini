<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Non-sport category modules + cross-category modules.
 * Urutan penting: ticket_types dibuat sebelum participants
 * (sudah di migration sebelumnya via FK), di sisa di sini.
 */
return new class extends Migration {
    public function up(): void
    {
        // ===== FESTIVAL =====
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('category')->nullable(); // Kuliner, UMKM, Fashion
            $table->text('description')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('booth_number')->nullable();
            $table->timestamps();
            $table->index('event_id');
        });

        Schema::create('stage_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->dateTime('time');
            $table->string('title');
            $table->string('performer')->nullable();
            $table->string('stage')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index('event_id');
        });

        // ===== MICE =====
        Schema::create('speakers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('title')->nullable(); // "CEO PT X", "Prof. Dr."
            $table->text('bio')->nullable();
            $table->string('photo_url')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
            $table->index('event_id');
        });

        // Catatan: tabel ini sengaja dinamai `event_sessions` (bukan `sessions`)
        // untuk menghindari bentrokan dengan tabel Laravel session driver (database).
        Schema::create('event_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->string('room')->nullable();
            $table->foreignId('speaker_id')->nullable()->constrained()->nullOnDelete();
            $table->string('track')->nullable(); // "Track A", "Keynote", "Workshop"
            $table->integer('capacity')->nullable();
            $table->timestamps();
            $table->index('event_id');
            $table->index('speaker_id');
        });

        // ticket_types dibuat di migration 000003b (sebelum participants)

        // ===== TRAVEL =====
        Schema::create('itinerary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->integer('day'); // hari ke-1, ke-2
            $table->dateTime('time');
            $table->string('title');
            $table->string('location')->nullable();
            $table->string('notes')->nullable();
            $table->string('transport_mode')->nullable(); // Bus, Pesawat
            $table->timestamps();
            $table->index('event_id');
        });

        // ===== CROSS-CATEGORY =====
        Schema::create('galleries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('url');
            $table->string('caption')->nullable();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->index('event_id');
        });

        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->string('template_url')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->string('code')->unique(); // untuk verifikasi publik
            $table->timestamps();
            $table->index('event_id');
            $table->index('participant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
        Schema::dropIfExists('galleries');
        Schema::dropIfExists('itinerary_items');
        Schema::dropIfExists('event_sessions');
        Schema::dropIfExists('speakers');
        Schema::dropIfExists('stage_programs');
        Schema::dropIfExists('tenants');
    }
};
