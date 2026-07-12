<?php

use App\Models\Event;
use App\Models\Medal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

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
        ->sortBy(
            // Olympic medal ordering: gold dominan, tie-break silver, lalu bronze.
            // Pakai weighted score supaya urutan benar (mis. 2G0S0B > 1G3S0B).
            fn ($s) => [$s['gold'] * 1000000 + $s['silver'] * 1000 + $s['bronze']],
            SORT_REGULAR,
            true, // descending
        )
        ->values()
        ->map(fn($s, $i) => array_merge($s, ['rank' => $i + 1]));

    return response()->json([
        'event' => $event,
        'stats' => $stats,
        'standings' => $standings,
    ]);
});

// ===== BRACKET =====
// Bracket untuk sebuah division — group by stage untuk display
Route::get('/divisions/{division}/bracket', function (\App\Models\Division $division) {
    // P3-1: jangan expose division dari event non-publik
    if (!$division->event?->is_public) {
        return response()->json(['message' => 'Division tidak ditemukan'], 404);
    }

    $matches = \App\Models\MatchModel::where('division_id', $division->id)
        ->with([
            'participantA' => fn($q) => $q->select('id', 'name')->with('contingent:id,name'),
            'participantB' => fn($q) => $q->select('id', 'name')->with('contingent:id,name'),
            'winner:id',
        ])
        ->orderByRaw("CASE round
            WHEN 'GROUP_STAGE' THEN 1
            WHEN 'ROUND_OF_16' THEN 2
            WHEN 'QUARTERFINAL' THEN 3
            WHEN 'SEMIFINAL' THEN 4
            WHEN 'THIRD_PLACE' THEN 5
            WHEN 'FINAL' THEN 6
            ELSE 7 END")
        ->orderBy('group_label')
        ->orderBy('bracket_position')
        ->get();

    // group by stage
    $byStage = [];
    foreach ($matches as $m) {
        $stage = ($m->group_label && $m->round === 'GROUP_STAGE')
            ? "Grup {$m->group_label}"
            : str_replace('_', ' ', $m->round);
        $byStage[$stage][] = $m;
    }

    return response()->json([
        'division' => $division->only(['id', 'discipline', 'age_category', 'gender', 'class_name', 'format']),
        'stages' => $byStage,
    ]);
});

// ===== REGISTER PARTICIPANT (public) =====
Route::post('/events/{slug}/register', function (string $slug, \Illuminate\Http\Request $request) {
    $event = \App\Models\Event::where('slug', $slug)->where('is_public', true)->first();
    if (!$event) {
        return response()->json(['message' => 'Event tidak ditemukan'], 404);
    }
    if (!in_array($event->status, ['REGISTRATION_OPEN', 'DRAFT'])) {
        return response()->json(['message' => 'Pendaftaran event ini sudah ditutup'], 422);
    }

    $data = $request->validate([
        'name' => 'required|string|min:3|max:100',
        'gender' => 'required|in:PUTRA,PUTRI,MIXED',
        'birth_date' => 'required|date',
        'email' => 'nullable|email',
        'phone' => 'nullable|string|min:8',
        // P1-5: scope ke event_id supaya tidak bisa tebak ID dari event lain
        'contingent_id' => [
            'required',
            Rule::exists('contingents', 'id')->where('event_id', $event->id),
        ],
        'division_id' => [
            'required',
            Rule::exists('divisions', 'id')->where('event_id', $event->id),
        ],
    ]);

    // cek duplikasi
    $exists = \App\Models\Participant::where('name', $data['name'])
        ->where('division_id', $data['division_id'])
        ->exists();
    if ($exists) {
        return response()->json(['message' => 'Peserta sudah terdaftar di kelas ini'], 422);
    }

    $participant = \App\Models\Participant::create([
        'event_id' => $event->id,
        'division_id' => $data['division_id'],
        'contingent_id' => $data['contingent_id'],
        'name' => $data['name'],
        'gender' => $data['gender'],
        'birth_date' => $data['birth_date'],
        'email' => $data['email'] ?? null,
        'phone' => $data['phone'] ?? null,
        'status' => 'PENDING',
    ]);

    return response()->json([
        'message' => 'Pendaftaran berhasil. Status: menunggu verifikasi.',
        'participant_id' => $participant->id,
    ], 201);
});

// ===== EVENTS LIST DETAIL: kontingen + divisions (untuk form register) =====
Route::get('/events/{slug}/register-form', function (string $slug) {
    $event = \App\Models\Event::where('slug', $slug)->where('is_public', true)->first();
    if (!$event) {
        return response()->json(['message' => 'Event tidak ditemukan'], 404);
    }

    $divisions = $event->divisions()
        ->orderBy('discipline')->orderBy('age_category')->orderBy('gender')
        ->get(['id', 'discipline', 'age_category', 'gender', 'class_name', 'format']);

    $contingents = $event->contingents()->orderBy('name')->get(['id', 'name', 'type']);

    return response()->json([
        'event' => $event->only(['id', 'name', 'slug', 'status']),
        'divisions' => $divisions,
        'contingents' => $contingents,
        'registration_open' => in_array($event->status, ['REGISTRATION_OPEN', 'DRAFT']),
    ]);
});

// Health check
Route::get('/health', fn() => response()->json(['status' => 'ok', 'time' => now()]));
