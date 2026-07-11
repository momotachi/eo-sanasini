<?php

use App\Models\Event;
use App\Models\Medal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public API Routes — dikonsumsi Nuxt frontend
|--------------------------------------------------------------------------
*/

Route::get('/events', function (Request $request) {
    $query = Event::query()
        ->where('is_public', true)
        ->with(['organization:id,name,slug']);

    // filter by category (sport, festival, mice, other)
    if ($cat = $request->string('category')->toString()) {
        $query->where('category', strtoupper($cat));
    }

    // filter by status
    if ($status = $request->string('status')->toString()) {
        $query->where('status', strtoupper($status));
    }

    $events = $query->orderByRaw("CASE status
        WHEN 'ONGOING' THEN 1
        WHEN 'REGISTRATION_OPEN' THEN 2
        WHEN 'UPCOMING' THEN 3
        WHEN 'COMPLETED' THEN 4
        ELSE 5 END")
        ->orderBy('start_date', 'desc')
        ->paginate(12);

    return response()->json($events);
});

Route::get('/events/{slug}', function (string $slug) {
    $event = Event::where('slug', $slug)
        ->where('is_public', true)
        ->with([
            'config',
            'contingents' => fn($q) => $q->orderBy('name'),
            'divisions' => fn($q) => $q->orderBy('discipline')->orderBy('age_category')->orderBy('gender'),
            'venues',
            'schedule' => fn($q) => $q->orderBy('time'),
            'organization',
        ])
        ->first();

    if (!$event) {
        return response()->json(['message' => 'Event tidak ditemukan'], 404);
    }

    // stats
    $stats = [
        'participants' => $event->participants()->where('status', 'APPROVED')->count(),
        'contingents' => $event->contingents()->count(),
        'matches' => \App\Models\MatchModel::whereHas('division', fn($q) => $q->where('event_id', $event->id))->count(),
    ];

    // contingent standings (auto-calculated dari medals)
    $standings = Medal::where('event_id', $event->id)
        ->whereNotNull('contingent_id')
        ->with('contingent:id,name,logo_url')
        ->get()
        ->groupBy('contingent_id')
        ->map(function ($medals, $contingentId) {
            $contingent = $medals->first()->contingent;
            return [
                'contingent' => $contingent ? ['id' => $contingent->id, 'name' => $contingent->name, 'logo_url' => $contingent->logo_url] : null,
                'gold' => $medals->where('type', 'GOLD')->count(),
                'silver' => $medals->where('type', 'SILVER')->count(),
                'bronze' => $medals->where('type', 'BRONZE')->count(),
                'total' => $medals->count(),
            ];
        })
        ->sortByDesc('gold')->sortByDesc('silver')->sortByDesc('bronze')
        ->values()
        ->map(fn($s, $i) => array_merge($s, ['rank' => $i + 1]));

    return response()->json([
        'event' => $event,
        'stats' => $stats,
        'standings' => $standings,
    ]);
});

// Health check
Route::get('/health', fn() => response()->json(['status' => 'ok', 'time' => now()]));
