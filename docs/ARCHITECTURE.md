# EO Sanasini — Architecture & Decisions Log

| | |
|---|---|
| **Stack final** | Laravel 12 (backend + Filament admin) + Nuxt 4 (frontend) |
| **Database** | PostgreSQL 16 (Docker, port 5433) |
| **Repo** | https://github.com/momotachi/eo-sanasini |
| **Server** | 194.233.90.53 (shared dengan MES, isolasi port) |
| **Maintainer** | Owner + AI agent (ongoing feature dev) |

> Dokumen ini adalah **bible project** — semua keputusan arsitektural, data model,
> dan design system ada di sini. Dipakai sebagai sumber kebenaran untuk porting
> dari Next.js (yang di-retire) ke Laravel+Nuxt.

---

## 1. Kenapa Laravel + Nuxt (bukan Next.js)

**Konteks**: MES (project lain di VPS yang sama) sudah pakai **Laravel + Nuxt**.
Walaupun Sanasini berbeda project dengan MES, pemilihan stack yang konsisten
memudahkan maintenance oleh owner + AI agent.

- **Laravel + Filament** → admin panel auto-generated super cepat (CRUD peserta, event,
  tenant, speaker, schedule)
- **Nuxt/Vue** → frontend publik, SEO, sama-sama capable dengan React untuk UI keren
- **1 mental model** untuk AI agent → tidak context-switch antar paradigm
- **Next.js yang sebelumnya jalan** → di-retire, knowledge di-port ke sini

## 2. Struktur Repo

```
eo-sanasini/
├── backend/                # Laravel 12 + Filament
│   ├── app/Models/         # Eloquent models
│   ├── app/Filament/       # Admin resources (auto-CRUD)
│   ├── database/migrations/
│   ├── routes/api.php      # REST API untuk Nuxt
│   └── ...
├── frontend/               # Nuxt 4 + Vue 3
│   ├── app/pages/          # Routes (file-based)
│   ├── app/components/     # Vue components
│   ├── nuxt.config.ts
│   └── ...
├── docs/                   # PRD, PLAN, ARCHITECTURE (this)
├── docker-compose.yml      # postgres + adminer
└── README.md
```

### Akses
- **Public web (Nuxt):** `http://194.233.90.53:3001` (port 3001 — tidak bentrok MES di 3000)
- **Admin (Filament):** `http://194.233.90.53:8001` (Laravel serve)
- **API (Laravel):** `http://194.233.90.53:8001/api/*`
- **PostgreSQL:** port 5433 (Docker)
- **Adminer (DB UI):** `http://194.233.90.53:8080`

---

## 3. Data Model (full schema)

> Disadur dari `schema.prisma` (Next.js era), di-port ke Laravel migrations.
> Field name pakai snake_case (Laravel convention).

### 3.1 Organization & Events

**organizations**
- id (uuid/char(26)), name, slug (unique), tagline, about (text), logo_url,
  website, instagram, email, phone, address, timestamps

**events**
- id, name, slug (unique), type (enum: CHAMPIONSHIP/LEAGUE/FESTIVAL/MICE/OTHER),
  **category (enum: SPORT/FESTIVAL/MICE/OTHER)** ← menentukan modul yang relevan
  **modules (json)** ← toggle: { registration, schedule, gallery, certificate, livestream, merch }
  status (enum: DRAFT/REGISTRATION_OPEN/UPCOMING/ONGOING/COMPLETED/CANCELLED),
  description (text), poster_url, start_date, end_date, venue, address, map_url,
  contact_name, contact_phone, contact_email, is_public (bool default true),
  organization_id (FK), timestamps

### 3.2 Event Configuration

**event_configs** (1:1 dengan event)
- event_id (FK unique), registration_type (enum: INDIVIDUAL/TEAM/HYBRID),
  bronze_per_division (int default 2), age_categories (json),
  disciplines (json), extra_config (json), timestamps

### 3.3 Contingent (Kontingen)

**contingents**
- id, event_id (FK cascade), name, type (enum: CLUB/PROVINCE/COUNTRY/OTHER),
  logo_url, contact_name, contact_phone, timestamps
- unique: [event_id, name]

### 3.4 Division (pool/class — sport only)

**divisions**
- id, event_id (FK cascade), discipline (string), age_category (string),
  gender (enum: PUTRA/PUTRI/MIXED), class_name (string),
  format (enum: FULL_KNOCKOUT/GROUP_KNOCKOUT/ROUND_ROBIN/SCORING/NON_COMPETITIVE),
  scoring_config (json, nullable), timestamps
- unique: [event_id, discipline, age_category, gender, class_name]

### 3.5 Participant (GENERAL — bisa atlet/pengunjung/attendee/peserta trip)

**participants**
- id, event_id (FK cascade), division_id (FK cascade, **nullable** — wajib hanya sport),
  contingent_id (FK nullable), ticket_type_id (FK nullable),
  name, gender (enum default PUTRA), birth_date (date nullable),
  email, phone, job_title (nullable — MICE), id_doc_number (nullable — travel),
  emergency_contact (nullable — travel), document_url (nullable),
  seed (int nullable), status (enum: PENDING/APPROVED/REJECTED/WITHDRAWN default PENDING),
  meta (json nullable), timestamps

### 3.6 Match (pertandingan — sport only)

**matches**
- id, division_id (FK cascade), round (enum: GROUP_STAGE/ROUND_OF_16/QUARTERFINAL/SEMIFINAL/FINAL/THIRD_PLACE),
  bracket_position (int), group_label (nullable),
  participant_a_id (FK nullable), participant_b_id (FK nullable),
  score_a (json nullable), score_b (json nullable),
  winner_id (FK nullable), status (enum: SCHEDULED/ONGOING/COMPLETED/BYE default SCHEDULED),
  venue_id (FK nullable), scheduled_at (nullable), notes (nullable), timestamps

### 3.7 Medal (medali — sport only)

**medals**
- id, event_id (FK), division_id (FK), participant_id (FK),
  contingent_id (FK nullable), type (enum: GOLD/SILVER/BRONZE), discipline (string), timestamps
- unique: [division_id, participant_id, type]

### 3.8 Venue & Schedule (all categories)

**venues** — id, event_id (FK cascade), name, area (nullable), timestamps, unique: [event_id, name]

**schedule_items** — id, event_id (FK cascade), time (datetime), title, venue_id (FK nullable),
  division (nullable), notes (nullable), timestamps

### 3.9 Auth & RBAC

**users**
- id, email (unique), name, password_hash (password), role (enum: SUPER_ADMIN/ADMIN/STAF default STAF),
  is_active (bool default true), last_login_at (nullable), timestamps

**event_staff** (junction STAF ↔ event)
- id, user_id (FK cascade), event_id (FK cascade), assigned_at, unique: [user_id, event_id]

**Role semantics:**
| Role | Bisa | Tidak bisa |
|---|---|---|
| SUPER_ADMIN | Semua — user/role management, system config, semua event | — |
| ADMIN | Manage semua event, assign staf | User/role mgmt, system config |
| STAF | Manage hanya event yang di-assign | Create/delete event, akses event lain |

### 3.10 Festival Module

**tenants** — id, event_id (FK cascade), name, category, description (text), logo_url,
  contact_name, contact_phone, booth_number, timestamps

**stage_programs** — id, event_id (FK cascade), time (datetime), title, performer, stage,
  description (text nullable), timestamps

### 3.11 MICE Module

**speakers** — id, event_id (FK cascade), name, title, bio (text), photo_url, email, timestamps

**sessions** — id, event_id (FK cascade), title, description (text), start_time, end_time,
  room, speaker_id (FK set null), track, capacity (int nullable), timestamps

**ticket_types** — id, event_id (FK cascade), name, price (decimal 12,2), currency (default IDR),
  quota (int nullable), sold_count (int default 0), description, sale_start, sale_end, timestamps

### 3.12 Travel Module

**itinerary_items** — id, event_id (FK cascade), day (int), time (datetime), title, location,
  notes, transport_mode, timestamps

### 3.13 Cross-Category Modules

**galleries** — id, event_id (FK cascade), url, caption, uploaded_at
**certificates** — id, event_id (FK cascade), participant_id (FK cascade), template_url,
  issued_at (nullable), code (unique), timestamps

---

## 4. Design System

### 4.1 Palette (Professional & Elegant)

Palet: warm off-white background + deep ink text + **deep gold/bronze accent**
(mencerminkan prestise 15+ tahun + kejuaraan). Dark mode opsional.

```
Light mode:
  --background: hsl(40 33% 98%)    /* warm off-white */
  --foreground: hsl(222 25% 11%)   /* deep ink */
  --primary:    hsl(38 64% 30%)    /* deep gold/bronze */
  --muted:      hsl(220 14% 94%)
  --accent:     hsl(38 58% 92%)    /* soft gold tint */
  --border:     hsl(220 13% 88%)

Dark mode:
  --background: hsl(222 25% 8%)
  --primary:    hsl(38 70% 52%)    /* gold pop on dark */
```

### 4.2 Fonts

- **Serif (headings):** Playfair Display → nama brand, judul section, "premium feel"
- **Sans (body):** Inter → body text, UI

### 4.3 Komponen (porting ke Vue)

Komponen yang sudah jadi di Next.js, perlu port ke Vue equivalent:
- Navbar (sticky, blur background)
- Footer (kontak, sosmed, nav)
- Hero (badge "sejak 2009", 2 CTA)
- Stats (4 angka: 15+ tahun, 500+ event, 50K+ peserta, 100+ kontingen)
- About (4 pillar: Kurasi Premium, Tim Berpengalaman, Eksekusi Terpercaya, Jangkauan Nasional)
- Services (4 kartu: Sport, MICE, Festival, Travel)
- Portfolio (3 kartu: Pekan Raya, Feskul, Sanasini Taekwondo)
- Testimonials (3 quote)
- CTA (banner gold "Punya event yang ingin diwujudkan?")
- EventCard, MedalTable, DivisionList, ScheduleTimeline, BracketView, BracketExplorer

---

## 5. Bracket Engine Logic (port ke PHP class)

Pure function logic (framework-agnostic), sudah teruji di Next.js:

```typescript
generateKnockoutSlots(participantIds)    // single elimination, auto BYE
generateRoundRobinSlots(participantIds)  // semua lawan semua
splitIntoGroups(participantIds, n)       // bagi ke n grup
seedOrder(slots)                         // seeding standar (1v8, 4v5)
```

→ Port ke `app/Services/BracketEngine.php` sebagai service class.

---

## 6. Event Creation Flow (Fase B — wizard)

5 step: Pilih Kategori → Info Dasar → Konfigurasi Spesifik → Modul Tambahan → Review.

Detail: lihat `docs/PLAN-event-creation-flow.md`.

---

## 7. Keputusan Tercatat

| # | Keputusan | Alasan |
|---|---|---|
| 1 | Stack: Laravel + Nuxt (bukan Next.js) | Konsistensi dengan MES, AI agent maintain |
| 2 | Admin: Filament (bukan hand-build) | CRUD auto-generated super cepat |
| 3 | Storage: VPS local disk (`/var/www/eo-sanasini/uploads/`) | Simplisitas, upload via Laravel |
| 4 | Payment: Mock dulu (no real gateway) | Belum prioritas |
| 5 | Currency: IDR saja (field currency tetap utk fleksibilitas) | Market Indonesia |
| 6 | Role: 3-tier (SUPER_ADMIN/ADMIN/STAF) + EventStaff | RBAC granular per event |
| 7 | Database: PostgreSQL 16 Docker (port 5433) | Tidak ganggu host postgres (5432) |
| 8 | Public port: 3001, Admin port: 8001 | Tidak bentrok MES (3000) |
