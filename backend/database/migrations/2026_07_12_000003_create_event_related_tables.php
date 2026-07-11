<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tables yang selalu relate ke Event (multi-category):
 *   - event_configs (1:1)
 *   - contingents
 *   - venues
 *   - schedule_items
 *   - event_staff (RBAC junction)
 */
return new class extends Migration {
    public function up(): void
    {
        // EventConfig (1:1 dengan event)
        Schema::create('event_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('registration_type', 20)->default('HYBRID'); // INDIVIDUAL|TEAM|HYBRID
            $table->integer('bronze_per_division')->default(2);
            $table->json('age_categories')->nullable();
            $table->json('disciplines')->nullable();
            $table->json('extra_config')->nullable();
            $table->timestamps();
        });

        // Contingent (sport, tapi bisa untuk grouping kontingen festival/MICE)
        Schema::create('contingents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type', 20)->default('CLUB'); // CLUB|PROVINCE|COUNTRY|OTHER
            $table->string('logo_url')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'name']);
            $table->index('event_id');
        });

        // Venue
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('area')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'name']);
        });

        // Schedule (semua kategori)
        Schema::create('schedule_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->dateTime('time');
            $table->string('title');
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $table->string('division')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index('event_id');
        });

        // EventStaff (RBAC junction: STAF ↔ event)
        Schema::create('event_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();

            $table->unique(['user_id', 'event_id']);
            $table->index('event_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_staff');
        Schema::dropIfExists('schedule_items');
        Schema::dropIfExists('venues');
        Schema::dropIfExists('contingents');
        Schema::dropIfExists('event_configs');
    }
};
