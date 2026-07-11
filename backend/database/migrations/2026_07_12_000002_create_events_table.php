<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            // type & category memakai string + check constraint untuk fleksibilitas enum
            $table->string('type', 30)->default('CHAMPIONSHIP'); // CHAMPIONSHIP|LEAGUE|FESTIVAL|MICE|OTHER
            $table->string('category', 20)->default('SPORT');    // SPORT|FESTIVAL|MICE|OTHER
            $table->json('modules')->nullable();                  // toggle modul
            $table->string('status', 30)->default('DRAFT');      // DRAFT|REGISTRATION_OPEN|UPCOMING|ONGOING|COMPLETED|CANCELLED
            $table->text('description')->nullable();
            $table->string('poster_url')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('venue')->nullable();
            $table->string('address')->nullable();
            $table->string('map_url')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->index('organization_id');
            $table->index('status');
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
