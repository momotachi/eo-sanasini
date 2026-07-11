<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sport module: divisions, participants, matches, medals.
 * Participants di-generalisasi (event_id wajib; division_id nullable
 * supaya bisa dipakai untuk pengunjung/attendee/peserta trip).
 */
return new class extends Migration {
    public function up(): void
    {
        // Division (pool/class)
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('discipline');           // Kyorugi, Poomsae, Futsal, ...
            $table->string('age_category');         // Cadet, Junior, Senior
            $table->string('gender', 10)->default('PUTRA'); // PUTRA|PUTRI|MIXED
            $table->string('class_name');           // "-45kg", "Individual"
            $table->string('format', 30)->default('GROUP_KNOCKOUT'); // FULL_KNOCKOUT|GROUP_KNOCKOUT|ROUND_ROBIN|SCORING|NON_COMPETITIVE
            $table->json('scoring_config')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'discipline', 'age_category', 'gender', 'class_name'], 'uniq_division');
            $table->index('event_id');
        });

        // Participant (GENERAL — bisa atlet/pengunjung/attendee/peserta trip)
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('division_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('contingent_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('ticket_type_id')->nullable()->constrained('ticket_types')->nullOnDelete();
            $table->string('name');
            $table->string('gender', 10)->default('PUTRA');
            $table->date('birth_date')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('job_title')->nullable();           // MICE
            $table->string('id_doc_number')->nullable();       // travel (KTP/passport)
            $table->string('emergency_contact')->nullable();   // travel
            $table->string('document_url')->nullable();
            $table->integer('seed')->nullable();
            $table->string('status', 20)->default('PENDING'); // PENDING|APPROVED|REJECTED|WITHDRAWN
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('event_id');
            $table->index('division_id');
            $table->index('contingent_id');
            $table->index('ticket_type_id');
        });

        // Match
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->string('round', 30);             // GROUP_STAGE|ROUND_OF_16|QUARTERFINAL|SEMIFINAL|FINAL|THIRD_PLACE
            $table->integer('bracket_position');
            $table->string('group_label', 5)->nullable();
            $table->foreignId('participant_a_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->foreignId('participant_b_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->json('score_a')->nullable();
            $table->json('score_b')->nullable();
            $table->foreignId('winner_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->string('status', 20)->default('SCHEDULED'); // SCHEDULED|ONGOING|COMPLETED|BYE
            $table->foreignId('venue_id')->nullable()->constrained()->nullOnDelete();
            $table->dateTime('scheduled_at')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index('division_id');
            $table->index('status');
        });

        // Medal
        Schema::create('medals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('division_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contingent_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 10); // GOLD|SILVER|BRONZE
            $table->string('discipline');
            $table->timestamps();

            $table->unique(['division_id', 'participant_id', 'type'], 'uniq_medal');
            $table->index('event_id');
            $table->index('contingent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medals');
        Schema::dropIfExists('matches');
        Schema::dropIfExists('participants');
        Schema::dropIfExists('divisions');
    }
};
