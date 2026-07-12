# Task Dev — Bug Fixes & Pengerjaan Lanjutan

| | |
|---|---|
| **Dokumen** | Daftar task untuk developer: perbaikan bug + penyelesaian fitur |
| **Status** | Ready untuk dikerjakan |
| **Tanggal** | 12 Juli 2026 |
| **Sumber temuan** | Audit menyeluruh backend (Laravel/Filament) + frontend (Nuxt) tanggal 12 Juli 2026 |
| **Metode audit** | Build frontend (sukses) + analisis statis backend (PHP dijalankan di VPS) |

> **Cara baca:** Task diurutkan berdasarkan **prioritas P0 → P3**. Kerjakan dari atas. Setiap task punya: *Gejala*, *Penyebab*, *Langkah*, *Code snippet*, dan *Definition of Done (DoD)*.
>
> **Test wajib setelah tiap P0/P1:** jalankan `php artisan migrate:fresh --seed` lalu buka `/admin` dan `/api/events` untuk memastikan tidak ada regresi.

---

## 0. Prasyarat Sebelum Mulai

```bash
# SSH ke VPS dulu (backend jalan di sana)
ssh root@194.233.90.53
cd /var/www/eo-sanasini

# Selalu mulai dari branch baru
git checkout main && git pull
git checkout -b fix/audit-bugs-p0
```

---

## 🔴 P0 — Bug Kritis (Aplikasi Tidak Bisa Jalan)

### Task P0-1: Buat file `routes/console.php` yang hilang

**Gejala:** Laravel gagal boot / `php artisan` command error: *"file not found routes/console.php"*.

**Penyebab:** `backend/bootstrap/app.php:12` mereferensikan `commands: __DIR__.'/../routes/console.php'`, tapi file tersebut tidak ada (terhapus saat setup awal).

**Langkah:**

1. Buat file `backend/routes/console.php` dengan isi minimal:

```php
<?php

use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
| Di sini bisa daftarkan schedule & closure-based artisan command.
| Saat ini kosong — ditaruh agar bootstrap/app.php tidak error.
*/

// Contoh (uncomment kalau perlu):
// Schedule::command('inspire')->hourly();
```

**DoD:**
- [ ] File `backend/routes/console.php` ada.
- [ ] `php artisan list` berjalan tanpa error.
- [ ] `php artisan serve` bisa start.

---

### Task P0-2: Hapus / kosongkan route `/` yang memanggil `welcome` view

**Gejala:** Akses `http://194.233.90.53:8001/` → error 500 *"View [welcome] not found"* (folder `resources/views/` tidak ada).

**Penyebab:** `backend/routes/web.php` memanggil `view('welcome')`, tapi `resources/views/welcome.blade.php` hilang. Frontend publik sudah di-handle Nuxt di port 3001, jadi route ini tidak diperlukan.

**Langkah:**

1. Ganti isi `backend/routes/web.php` menjadi:

```php
<?php

use Illuminate\Support\Facades\Route;

// Frontend publik di-handle Nuxt (port 3001).
// API ada di /api/* (routes/api.php). Admin di /admin (Filament).
// Route root "/" sengaja kosong; bisa diarahkan ke /admin kalau perlu:
Route::redirect('/', '/admin');
```

2. Pastikan tidak ada view lain yang direferensikan dari `web.php`.

**DoD:**
- [ ] Akses `/` mengarah ke `/admin` (atau minimal tidak 500).
- [ ] `/api/*` dan `/admin` tetap jalan.

---

### Task P0-3: Lengkapi 15 class Resource Filament yang belum terdaftar

**Gejala:** Panel admin `/admin` **hanya menampilkan menu "Event"**. Modul-modul inti (Division, Participant, Match, Medal, Contingent, Organization, Schedule, Speaker, dst) tidak muncul, padahal UI-nya (Form + Table) sudah dibuat lengkap.

**Penyebab:** Hanya `app/Filament/Resources/Events/EventResource.php` yang punya class `extends Resource`. 15 resource lain hanya punya folder `Schemas/` + `Tables/` **tanpa class Resource utama**, sehingga Filament tidak mendaftarkannya.

**Resource yang harus dibuat** (cek dengan: `grep -rln "extends Resource" app/Filament/Resources/`):

| # | Nama Class | Lokasi file | Model |
|---|---|---|---|
| 1 | `OrganizationResource` | `app/Filament/Resources/Organizations/OrganizationResource.php` | `Organization` |
| 2 | `ContingentResource` | `app/Filament/Resources/Contingents/ContingentResource.php` | `Contingent` |
| 3 | `DivisionResource` | `app/Filament/Resources/Divisions/DivisionResource.php` | `Division` |
| 4 | `ParticipantResource` | `app/Filament/Resources/Participants/ParticipantResource.php` | `Participant` |
| 5 | `MatchModelResource` | `app/Filament/Resources/MatchModels/MatchModelResource.php` | `MatchModel` |
| 6 | `MedalResource` | `app/Filament/Resources/Medals/MedalResource.php` | `Medal` |
| 7 | `VenueResource` | `app/Filament/Resources/Venues/VenueResource.php` | `Venue` |
| 8 | `ScheduleItemResource` | `app/Filament/Resources/ScheduleItems/ScheduleItemResource.php` | `ScheduleItem` |
| 9 | `UserResource` | `app/Filament/Resources/Users/UserResource.php` | `User` |
| 10 | `TenantResource` | `app/Filament/Resources/Tenants/TenantResource.php` | `Tenant` |
| 11 | `StageProgramResource` | `app/Filament/Resources/StagePrograms/StageProgramResource.php` | `StageProgram` |
| 12 | `SpeakerResource` | `app/Filament/Resources/Speakers/SpeakerResource.php` | `Speaker` |
| 13 | `EventSessionResource` | `app/Filament/Resources/EventSessions/EventSessionResource.php` | `EventSession` |
| 14 | `TicketTypeResource` | `app/Filament/Resources/TicketTypes/TicketTypeResource.php` | `TicketType` |
| 15 | `ItineraryItemResource` | `app/Filament/Resources/ItineraryItems/ItineraryItemResource.php` | `ItineraryItem` |

**Template class Resource** (sesuaikan namespace/model/icon per resource):

```php
<?php

namespace App\Filament\Resources\Divisions;

use App\Filament\Resources\Divisions\Pages\CreateDivision;
use App\Filament\Resources\Divisions\Pages\EditDivision;
use App\Filament\Resources\Divisions\Pages\ListDivisions;
use App\Filament\Resources\Divisions\Schemas\DivisionForm;
use App\Filament\Resources\Divisions\Tables\DivisionsTable;
use App\Models\Division;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DivisionResource extends Resource
{
    protected static ?string $model = Division::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquaresPlus;

    protected static ?string $navigationLabel = 'Kelas Pertandingan';
    protected static ?string $modelLabel = 'Kelas';
    protected static ?string $pluralModelLabel = 'Kelas Pertandingan';
    protected static string|\UnitEnum|null $navigationGroup = 'Sport Module';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return DivisionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DivisionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDivisions::route('/'),
            'create' => CreateDivision::route('/create'),
            'edit' => EditDivision::route('/{record}/edit'),
        ];
    }
}
```

> **Penting — grouping navigationGroup:** Kelompokkan supaya admin rapi:
> - `Manajemen Event`: Organization, Event, Venue, ScheduleItem
> - `Sport Module`: Division, Participant, MatchModel, Medal, Contingent
> - `Festival Module`: Tenant, StageProgram
> - `MICE Module`: Speaker, EventSession, TicketType
> - `Travel Module`: ItineraryItem
> - `Sistem`: User

**Daftar Heroicon yang disarankan:**

| Resource | Heroicon |
|---|---|
| Organization | `BuildingOffice2` |
| Contingent | `UserGroup` |
| Division | `SquaresPlus` |
| Participant | `Identification` |
| MatchModel | `Bolt` |
| Medal | `Trophy` |
| Venue | `MapPin` |
| ScheduleItem | `CalendarDays` |
| User | `Users` |
| Tenant | `BuildingStorefront` |
| StageProgram | `MusicalNote` |
| Speaker | `Microphone` |
| EventSession | `ClipboardDocumentList` |
| TicketType | `Ticket` |
| ItineraryItem | `Truck` / `Map` |

**DoD:**
- [ ] 15 file Resource utama dibuat (verifikasi: `grep -rln "extends Resource" app/Filament/Resources/` → harus 16 hasil termasuk EventResource).
- [ ] Buka `/admin` → semua menu muncul dan bisa diklik tanpa error.
- [ ] Tiap resource bisa Create / List / Edit (minimal 1 record test).

---

### Task P0-4: Buat Pages (List/Create/Edit) untuk SEMUA Resource

**Gejala:** Saat menu Resource dibuka → fatal error *"Class App\Filament\Resources\Events\Pages\ListEvents not found"*.

**Penyebab:** `EventResource::getPages()` merujuk `CreateEvent`, `EditEvent`, `ListEvents` di namespace `Events\Pages\`, tapi folder `Pages/` tidak ada di mana pun. Hal ini juga berlaku untuk 15 Resource baru (Task P0-3).

**Langkah:**

1. Untuk **tiap resource**, buat folder `Pages/` berisi 3 file. Contoh untuk Division:

**`app/Filament/Resources/Divisions/Pages/ListDivisions.php`**
```php
<?php

namespace App\Filament\Resources\Divisions\Pages;

use App\Filament\Resources\Divisions\DivisionResource;
use Filament\Resources\Pages\ListRecords;

class ListDivisions extends ListRecords
{
    protected static string $resource = DivisionResource::class;
}
```

**`app/Filament/Resources/Divisions/Pages/CreateDivision.php`**
```php
<?php

namespace App\Filament\Resources\Divisions\Pages;

use App\Filament\Resources\Divisions\DivisionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDivision extends CreateRecord
{
    protected static string $resource = DivisionResource::class;
}
```

**`app/Filament/Resources/Divisions/Pages/EditDivision.php`**
```php
<?php

namespace App\Filament\Resources\Divisions\Pages;

use App\Filament\Resources\Divisions\DivisionResource;
use Filament\Resources\Pages\EditRecord;

class EditDivision extends EditRecord
{
    protected static string $resource = DivisionResource::class;
}
```

2. Ulangi pola yang sama untuk semua 16 resource (termasuk `Events/Pages/{ListEvents,CreateEvent,EditEvent}`).

**DoD:**
- [ ] Semua resource punya 3 file Pages (total 48 file).
- [ ] Setiap menu resource: bisa buka list, klik Create, klik Edit record — tanpa error.
- [ ] Cek: `php artisan optimize:clear` lalu reload `/admin`.

---

### ✅ Validasi P0 (setelah Task P0-1 s/d P0-4 selesai)

```bash
cd /var/www/eo-sanasini/backend

# 1. Fresh DB + seed
php artisan migrate:fresh --seed

# 2. Clear cache
php artisan optimize:clear

# 3. Restart Laravel
supervisorctl restart eo-sanasini-laravel
```

- [ ] `php artisan serve` start tanpa error.
- [ ] `/admin` login berhasil (superadmin@sanasini.id / SuperAdmin2026!).
- [ ] Menu sidebar lengkap (Organization, Event, Division, Participant, Match, Medal, dst).
- [ ] Bisa Create record di ≥ 3 resource berbeda.
- [ ] `/api/events` mengembalikan JSON event seeder.

---

## 🟠 P1 — Bug Logika (Fitur Tidak Berfungsi Benar)

### Task P1-1: Perbaiki sorting klasemen kontingen (Olympic medal ordering)

**Gejala:** Klasemen medali di halaman event (`/events/{slug}`) urutannya salah — tidak mengikuti standar Olympic (gold dulu, tie-break silver, lalu bronze).

**Penyebab:** `backend/routes/api.php:81` chain `sortByDesc` yang saling menimpa:
```php
->sortByDesc('gold')->sortByDesc('silver')->sortByDesc('bronze')
```
Method `sortByDesc` Laravel Collection **mempertahankan urutan sebelumnya untuk key yang sama**, tapi rangkaian ini dievaluasi dari kiri ke kanan sehingga hasil akhir didominasi **gold saja** dengan tie-break aneh.

**Langkah:**

Ganti blok sorting di `routes/api.php` (sekitar baris 80-83):

```php
// SEBELUM
->sortByDesc('gold')->sortByDesc('silver')->sortByDesc('bronze')
->values()
->map(fn($s, $i) => array_merge($s, ['rank' => $i + 1]));

// SESUDAH
$values = $standings->sortBy(
    fn ($s) => [$s['gold'] * 1000000 + $s['silver'] * 1000 + $s['bronze']],
    SORT_REGULAR, true  // descending = true
)->values();

$standings = $values->map(fn($s, $i) => array_merge($s, ['rank' => $i + 1]));
```

**DoD:**
- [ ] Buat 3 kontingen dummy: A (2G 0S 0B), B (1G 3S 0B), C (2G 0S 1B).
- [ ] Cek `/api/events/{slug}` → urutan harus: C, A, B (C menang krn +1 bronze saat gold sama; A di atas B krn 2 gold > 1 gold meski B lebih banyak silver).
- [ ] Klasemen tampil benar di frontend.

---

### Task P1-2: Lengkapi auto-advance bracket knockout (round berikutnya)

**Gejala:** Bracket `FULL_KNOCKOUT` dengan >2 peserta **tidak lengkap**. Hanya round pertama (mis. QUARTERFINAL) yang ter-generate. SEMIFINAL & FINAL tidak pernah ada otomatis → medali otomatis tidak pernah ke-assign.

**Penyebab:** `BracketEngine::generateKnockoutSlots()` (`app/Services/BracketEngine.php:28-59`) hanya membuat match di round pertama. Tidak ada placeholder match untuk SEMIFINAL/FINAL. Saat `SetWinnerAction` menentukan pemenang, **tidak ada mekanisme** memindahkan winner ke match round berikutnya.

**Solusi (pilih satu):**

**Opsi A — Generate placeholder match untuk semua round (RECOMMENDED):**

Ubah `generateForDivision()` di `BracketEngine.php` agar setelah membuat round pertama, lanjut buat placeholder match kosong untuk SEMIFINAL, FINAL, THIRD_PLACE:

```php
if ($format === 'FULL_KNOCKOUT') {
    $slots = $this->generateKnockoutSlots($participants);
    $count = 0;
    foreach ($slots as $slot) {
        MatchModel::create(array_merge($slot, [
            'division_id' => $division->id,
            'bracket_position' => $slot['bracket_position'] ?? 0,
        ]));
        $count++;
    }

    // ===== TAMBAHAN: placeholder match untuk round selanjutnya =====
    $count = $this->generateNextRounds($division->id, $size, $count);

    $byeCount = count(array_filter($slots, fn($s) => $s['status'] === 'BYE'));
    return ['matches_count' => $count, 'message' => "{$count} pertandingan knockout dibuat ({$byeCount} BYE)."];
}
```

Tambah method helper:
```php
/**
 * Generate placeholder match untuk round berikutnya sampai FINAL + THIRD_PLACE.
 * Match-nya kosong (participant_a/b = null), akan diisi saat round sebelumnya selesai.
 */
protected function generateNextRounds(int $divisionId, int $firstRoundSize, int $count): int
{
    $roundsChain = [
        16 => ['ROUND_OF_16', 'QUARTERFINAL', 'SEMIFINAL', 'FINAL'],
        8  => ['QUARTERFINAL', 'SEMIFINAL', 'FINAL'],
        4  => ['SEMIFINAL', 'FINAL'],
        2  => ['FINAL'],
    ];

    $chain = $roundsChain[$firstRoundSize] ?? ['FINAL'];
    // skip index 0 (round pertama sudah dibuat)
    for ($i = 1; $i < count($chain); $i++) {
        $round = $chain[$i];
        $matchCountInRound = intdiv($firstRoundSize, pow(2, $i));
        for ($pos = 0; $pos < $matchCountInRound; $pos++) {
            MatchModel::create([
                'division_id' => $divisionId,
                'round' => $round,
                'bracket_position' => $pos,
                'participant_a_id' => null,
                'participant_b_id' => null,
                'status' => 'SCHEDULED',
            ]);
            $count++;
        }
    }

    // THIRD_PLACE match (dari 2 loser SEMIFINAL)
    if (in_array('SEMIFINAL', $chain)) {
        MatchModel::create([
            'division_id' => $divisionId,
            'round' => 'THIRD_PLACE',
            'bracket_position' => 0,
            'participant_a_id' => null,
            'participant_b_id' => null,
            'status' => 'SCHEDULED',
        ]);
        $count++;
    }

    return $count;
}
```

**Opsi B — Advance otomatis via Observer** (lebih kompleks):

Tambah logic di `MatchModelObserver::updated()` untuk mengisi `participant` match round berikutnya berdasarkan `bracket_position`.

**DoD:**
- [ ] Generate bracket pada Division dengan 8 peserta → ada match QUARTERFINAL (4), SEMIFINAL (2), FINAL (1), THIRD_PLACE (1) = 8 match.
- [ ] Saat semua QUARTERFINAL di-set winner → peserta SEMIFINAL terisi otomatis (kalau pakai Opsi B) atau bisa diisi manual di panel (kalau Opsi A).
- [ ] Setelah FINAL + THIRD_PLACE selesai → medali GOLD/SILVER/BRONZE ter-assign via `MedalService`.
- [ ] Cek klasemen kontingen muncul medalnya.

> **Catatan:** untuk Opsi A, advance-winner (mengisi match SEMIFINAL dari pemenang QUARTERFINAL) tetap perlu mekanisme. Opsi B via Observer lebih self-contained. Diskusikan dengan tim mau pakai mana sebelum implementasi.

---

### Task P1-3: Perbaiki auto-medal dari SEMIFINAL (saat bronzePerDivision = 2)

**Gejala:** Dua peraih bronze (loser semifinal) tidak dapat medali meski config `bronze_per_division = 2`.

**Penyebab:** Berhubungan dengan Task P1-2. Logika `MedalService::recomputeForDivision()` sudah benar (`api.php` tidak), tapi **tidak akan pernah trigger** karena SEMIFINAL match tidak ada. Setelah Task P1-2 selesai, ini seharusnya jalan otomatis. Tetap verifikasi:

**Langkah:**

1. Pastikan `app/Services/MedalService.php:57` blok ini aktif:
```php
if ($match->round === 'SEMIFINAL' && $bronzePerDivision === 2) {
    $loser = $this->getLoser($match);
    if ($loser) $this->createMedal($loser, $division, 'BRONZE');
}
```

2. Test: selesaikan kedua SEMIFINAL → harus muncul 2 medali BRONZE untuk 2 loser.

**DoD:**
- [ ] Setelah SEMIFINAL selesai (config bronze=2) → 2 loser dapat BRONZE.
- [ ] Setelah FINAL selesai → winner GOLD, loser SILVER.
- [ ] Setelah THIRD_PLACE selesai → winner BRONZE tambahan (total 4 medali: 1G 1S 2B).

---

### Task P1-4: Matikan pre-fill kredensial superadmin di production

**Gejala:** Halaman login admin production (`http://194.233.90.53:8001/admin`) **menampilkan email + password superadmin yang sudah terisi**, plus teks petunjuknya. Siapa pun yang buka URL bisa langsung klik "Masuk".

**Penyebab:** `app/Filament/Pages/Auth/Login.php:17` hardcode `$isDev = true`.

**Langkah:**

Ubah baris 17 menjadi:

```php
$isDev = app()->environment('local', 'testing', 'development');
```

Atau lebih ketat, hapus saja pre-fill-nya (dev tetap bisa ketik manual):

```php
public function form(Schema $schema): Schema
{
    return $schema
        ->schema([
            TextInput::make('email')
                ->label('Email')
                ->email()
                ->required()
                ->autocomplete('email'),
            TextInput::make('password')
                ->label('Password')
                ->password()
                ->required()
                ->autocomplete('current-password'),
        ])
        ->statePath('data');
}

public function getSubHeading(): ?string
{
    return null; // hapus teks petunjuk dev
}
```

**DoD:**
- [ ] Di production (`APP_ENV=production`), field login **kosong** dan tidak ada teks petunjuk.
- [ ] Di local (`APP_ENV=local`), pre-fill tetap berfungsi (kalau pakai opsi pertama).
- [ ] **Ganti password superadmin** setelah fix di-deploy (karena sudah pernah terekspos): `php artisan tinker` → `User::where('email','superadmin@sanasini.id')->update(['password'=>'PasswordBaru!']);`

---

### Task P1-5: Validasi pendaftaran — pastikan division & contingent milik event yang sama

**Gejala:** Peserta bisa mendaftar ke `division_id` / `contingent_id` dari **event lain** selama ID-nya valid. Cukup tebak ID dari event berbeda → lolos validasi.

**Penyebab:** `routes/api.php:144-145` hanya cek `exists:divisions,id` & `exists:contingents,id`, **tanpa scope ke `event_id`**.

**Langkah:**

Tambah import di atas `routes/api.php`:
```php
use Illuminate\Validation\Rule;
```

Ganti rule validasi (sekitar baris 138-146):

```php
$data = $request->validate([
    'name' => 'required|string|min:3|max:100',
    'gender' => 'required|in:PUTRA,PUTRI,MIXED',
    'birth_date' => 'required|date',
    'email' => 'nullable|email',
    'phone' => 'nullable|string|min:8',
    'contingent_id' => [
        'required',
        Rule::exists('contingents', 'id')->where('event_id', $event->id),
    ],
    'division_id' => [
        'required',
        Rule::exists('divisions', 'id')->where('event_id', $event->id),
    ],
]);
```

**DoD:**
- [ ] Daftar peserta dengan division_id event lain → 422 error (validation).
- [ ] Daftar peserta dengan division_id event yang sama → sukses.
- [ ] Frontend tidak crash saat menerima error validation baru.

---

## 🟡 P2 — Bug Ringan / Inkonsistensi (Hygiene)

### Task P2-1: Hapus `tailwind.config.ts` yang duplikat

**Gejala:** Ada dua config Tailwind: `tailwind.config.js` (dipakai, warna hardcoded) DAN `tailwind.config.ts` (dead code, pakai CSS vars yang tidak didefinisikan). Kontradiktif & membingungkan.

**Langkah:**

```bash
cd frontend
git rm tailwind.config.ts
```

Pertahankan `tailwind.config.js`. Bila ingin konsisten pakai CSS vars di `.ts`, pindahkan definisi variabel ke `app/assets/css/main.css` dulu (tambahan task opsional).

**DoD:**
- [ ] Hanya ada satu config Tailwind (`tailwind.config.js`).
- [ ] `npm run build` tetap sukses tanpa perubahan tampilan.

---

### Task P2-2: Sesuaikan `backend/.env.example` dengan stack sesungguhnya (PostgreSQL)

**Gejala:** `.env.example` default `DB_CONNECTION=sqlite`, padahal produksi pakai PostgreSQL 16. Developer baru yang setup dari `.env.example` dapat SQLite → potensi bug enum/check constraint dari seeder.

**Langkah:**

Edit `backend/.env.example`, bagian database:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5433
DB_DATABASE=eo_sanasini
DB_USERNAME=sanasini
DB_PASSWORD=sanasini_dev_2026
```

Tambahkan comment kalau perlu:
```env
# Untuk dev local bisa pakai Docker Postgres; lihat root .env (POSTGRES_*)
# Production: 194.233.90.53 port 5433
```

**DoD:**
- [ ] `.env.example` default pgsql.
- [ ] `cp .env.example .env && php artisan migrate` (di local dengan Postgres) sukses.

---

### Task P2-3: Tambah folder `tests/` minimal + 1 contoh test

**Gejala:** `composer.json` punya `phpunit/phpunit` + script `composer test`, tapi folder `tests/` **tidak ada**. `php artisan test` akan gagal.

**Langkah:**

1. Buat struktur:
```
backend/tests/
├── TestCase.php
├── Unit/
│   └── ExampleTest.php
└── Feature/
    └── ExampleTest.php
```

2. `tests/TestCase.php`:
```php
<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    //
}
```

3. `phpunit.xml` (buat kalau belum ada) — salin dari Laravel skeleton standar.

4. Tambah 1 test feature end-point `/api/health`:

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    public function test_health_endpoint(): void
    {
        $response = $this->getJson('/api/health');
        $response->assertOk()->assertJson(['status' => 'ok']);
    }
}
```

**DoD:**
- [ ] `php artisan test` berjalan tanpa "test not found".
- [ ] Test `/api/health` green.

---

### Task P2-4: Perbaiki dokumentasi PHP version di README

**Gejala:** `README.md:9` menulis "PHP 8.5", tapi `composer.json:9` require `php ^8.3`. PHP 8.5 belum rilis per Juli 2026.

**Langkah:**

Edit `README.md:9` tabel tech stack:

```markdown
| Backend | Laravel + Filament v4 | PHP 8.3+ / Laravel 13 |
```

**DoD:**
- [ ] Versi PHP di README konsisten dengan `composer.json` (`^8.3`).

---

## 🟢 P3 — Opsional / Nice-to-have

### Task P3-1: Scope route `/divisions/{division}/bracket` ke event publik

**Gejala:** Route `GET /api/divisions/{division}/bracket` pakai route-model binding tanpa cek `event.is_public`. Bisa dipakai enumerasi ID untuk akses division dari event non-publik.

**Langkah:**

Bungkus dengan cek:

```php
Route::get('/divisions/{division}/bracket', function (\App\Models\Division $division) {
    if (!$division->event?->is_public) {
        return response()->json(['message' => 'Division tidak ditemukan'], 404);
    }
    // ... sisanya tetap
});
```

**DoD:**
- [ ] Division dari event `is_public=false` → 404.

---

### Task P3-2: Tambah policy / authorization per resource (RBAC)

**Saat ini:** Semua user (SUPER_ADMIN, ADMIN, STAF) bisa kelola semua resource. Idealnya STAF hanya kelola event yang di-assign (lihat `User::canManageEvent()`).

**Langkah:**

1. Buat Policy per model: `php artisan make:policy DivisionPolicy --model=Division`.
2. Implementasi `viewAny`, `view`, `create`, `update`, `delete` dengan delegasi ke `User::isAdmin()` atau `canManageEvent()`.
3. Daftarkan di model: `protected static ?string $policy = DivisionPolicy::class;` (Filament auto-detect).

**Scope task ini besar** — pisahkan jadi task tersendiri kalau dikerjakan.

---

### Task P3-3: Tambah command artisan untuk generate bracket bulk

**Gejala:** Generate bracket harus satu-satu via UI. Berguna punya command:

```bash
php artisan bracket:generate {divisionId}
php artisan bracket:generate-all {eventId}
```

Bungkus `BracketEngine::generateForDivision()` dalam Command class. Berguna untuk cron/re-generate massal.

---

## 📊 Ringkasan Estimasi

| Prioritas | Jumlah task | Estimasi waktu | Risiko regresi |
|---|---|---|---|
| 🔴 P0 | 4 task | 4–8 jam (terutama P0-3 + P0-4 bikin ~64 file) | Tinggi — uji migrate:fresh + seed |
| 🟠 P1 | 5 task | 1–2 hari (P1-2 paling kompleks) | Sedang |
| 🟡 P2 | 4 task | 2–4 jam | Rendah |
| 🟢 P3 | 3 task | 1–2 hari (P3-2 besar) | — |

**Urutan rekomendasi:** P0-1 → P0-2 → (P0-3 + P0-4 paralel) → validasi P0 → P1-2 → P1-1 → P1-3 → P1-4 → P1-5 → P2 → P3.

---

## 🔗 Referensi

- Audit lengkap (hasil build + analisis statis): di arsip sesi audit 12 Juli 2026.
- Dokumen terkait: `docs/ARCHITECTURE.md`, `docs/PLAN-event-creation-flow.md`, `docs/PRD.md`.
- Stack & kredensial: `README.md`.
