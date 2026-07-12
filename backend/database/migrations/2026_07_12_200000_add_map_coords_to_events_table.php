<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambah kolom latitude, longitude, map_zoom untuk map picker di Event.
 * Bisa juga untuk Venue (lokasi arena spesifik).
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('map_url');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->unsignedSmallInteger('map_zoom')->default(13)->after('longitude');
        });

        Schema::table('venues', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('area');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'map_zoom']);
        });
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
