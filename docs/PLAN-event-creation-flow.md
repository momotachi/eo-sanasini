# Plan — Event Creation Flow & Multi-Category Engine

| | |
|---|---|
| **Dokumen** | Implementation Plan untuk Event Creation Wizard + Multi-Category Modules |
| **Status** | Draft untuk approval |
| **Tanggal** | 11 Juli 2026 |
| **Relasi** | Implementasi dari konsep di `PRD.md` section 3 (Event Engine) |

---

## 1. Masalah

Saat ini platform EO Sanasini:
- Event hanya bisa dibuat via **database seed manual** — tidak ada UI.
- Data model **mengasumsikan kompetisi olahraga** (Division, Match, Medal). Kalau dibuat event festival/konferensi, tabel itu tidak relevan tapi field wajibnya masih muncul.
- Registrasi peserta hanya cocok untuk **atlet sport** (minta kelas berat, kontingen).

Padahal EO Sanasini menangani **banyak jenis event**: Pekan Raya (festival), Feskul (olahraga), konferensi MICE, travel/gathering.

## 2. Solusi: Category-Driven Wizard

Pembuatan event dibagi jadi **wizard 5 langkah**. Langkah pertama menentukan **kategori**, dan langkah-langkah berikutnya menyesuaikan — modul yang tidak relevan untuk kategori itu **tidak dimunculkan**.

### Kategori event (4)

| Kategori | Contoh nyata | Kompetisi? | Modul inti |
|---|---|---|---|
| **🏆 Sport / Kompetisi** | Taekwondo Championship, Futsal League, E-sport | ✅ | Division, Match, Bracket, Medal, Standings |
| **🎪 Festival & Pameran** | Pekan Raya, Festival Seni, Bazaar | ❌ | Tenant/Vendor, Stage Program, Visitor Info |
| **💼 Konferensi / MICE** | Seminar, Workshop, Summit, AGM | ❌ | Speaker, Session/Track, Ticket, Certificate |
| **🎒 Travel / Gathering** | Trip kontingen, Retreat korporat, Family Day | ❌ | Itinerary, Participants, Logistics |

---

## 3. Flow Wizard (5 langkah)

```
┌─────────────────────────────────────────────────────────────┐
│  STEP 1 ─ Pilih Kategori                                     │
│  [🏆 Sport]  [🎪 Festival]  [💼 MICE]  [🎒 Lainnya]          │
│  → menentukan modul & field yang muncul di step 3 & 4        │
├─────────────────────────────────────────────────────────────┤
│  STEP 2 ─ Info Dasar (SAMA untuk semua kategori)             │
│  • Nama event, slug (auto dari nama)                         │
│  • Deskripsi, poster (upload / URL)                          │
│  • Tanggal mulai & selesai                                   │
│  • Lokasi, alamat, link peta                                 │
│  • Kontak panitia (nama, telp, email)                        │
│  • Apakah public? (visible di website atau private link)     │
├─────────────────────────────────────────────────────────────┤
│  STEP 3 ─ Konfigurasi Spesifik (BEDA per kategori)           │
│                                                               │
│  🏆 SPORT:                                                   │
│    • Cabang lomba (Kyorugi, Poomsae, futsal, dll)            │
│    • Format kompetisi per cabang:                            │
│        Full Knockout / Grup→Knockout / Liga / Scoring        │
│    • Tipe pendaftaran: Individual / Team / Hybrid            │
│    • Kategori umur (Pre-Teen, Cadet, Junior, Senior, ...)    │
│    • Kelas / divisi (berat badan, level, dll)                │
│    • Sistem medali (1 atau 2 bronze)                         │
│    • Auto-generate template Division dari kombinasi di atas  │
│                                                               │
│  🎪 FESTIVAL:                                                │
│    • Jumlah tenant & kategori tenant (kuliner, UMKM, dll)    │
│    • Program panggung (jadwal performa/hiburan)              │
│    • Apakah pengunjung perlu registrasi? (gratis/tiket/xtap) │
│    • Harga tiket masuk (jika ada)                            │
│                                                               │
│  💼 MICE:                                                    │
│    • Daftar pembicara (nama, topik, bio, foto)               │
│    • Sesi/acara (judul, waktu, ruang, pembicara)             │
│    • Jenis tiket: Early Bird / Regular / VIP + harga masing  │
│    • Apakah ada sertifikat digital peserta?                  │
│    • Kapasitas ruang                                         │
│                                                               │
│  🎒 LAINNYA:                                                 │
│    • Itinerary harian (tanggal, waktu, aktivitas, lokasi)    │
│    • Kuota peserta                                           │
│    • Logistik (transportasi, akomodasi, catering)            │
├─────────────────────────────────────────────────────────────┤
│  STEP 4 ─ Pilih Modul Tambahan (toggle on/off)               │
│  ☑ Pendaftaran online                                        │
│  ☑ Jadwal publik                                             │
│  ☐ Galeri foto                                               │
│  ☐ Sertifikat digital                                        │
│  ☐ Livestream link                                           │
│  ☐ Merchandise / e-commerce                                  │
│  (beberapa auto-on sesuai kategori)                          │
├─────────────────────────────────────────────────────────────┤
│  STEP 5 ─ Review & Publish                                   │
│  Preview seluruh input → Simpan sebagai DRAFT                │
│  → Publish (visible ke publik) saat siap                     │
└─────────────────────────────────────────────────────────────┘
```

---

## 4. Perubahan Data Model

### 4.1 Modifikasi tabel yang sudah ada

**`Event` table — tambah field:**
```
category     EventCategory   // SPORT | FESTIVAL | MICE | OTHER
modules      Json            // { registration, schedule, gallery, certificate, livestream, merch }
```

Enum baru:
```
enum EventCategory {
  SPORT
  FESTIVAL
  MICE
  OTHER
}
```

**`Participant` table — generalisasi:**
- Untuk sport: tetap dipakai sebagai "atlet" (terikat Division + Contingent)
- Untuk festival: dipakai sebagai "pengunjung terdaftar"
- Untuk MICE: dipakai sebagai "attendee"
- Untuk travel: dipakai sebagai "peserta trip"
- Field `divisionId` + `contingentId` jadi **nullable** (wajib hanya untuk sport)
- Tambah `registrationType` field per participant (mis. ticket type untuk MICE)

### 4.2 Tabel baru untuk non-sport

```
// === FESTIVAL ===
model Tenant {
  id          String   @id
  eventId     String
  name        String
  category    String   // "Kuliner", "UMKM", "Fashion", ...
  description String?
  logoUrl     String?
  contactName String?
  contactPhone String?
  boothNumber String?  // nomor stan
  createdAt   DateTime
}

model StageProgram {
  id          String   @id
  eventId     String
  time        DateTime
  title       String   // "Tari Saman", "Band XYZ"
  performer   String?
  stage       String?  // "Panggung Utama", "Panggung Kecil"
  description String?
}

// === MICE ===
model Speaker {
  id          String   @id
  eventId     String
  name        String
  title       String?  // "CEO PT X", "Prof. Dr."
  bio         String?
  photoUrl    String?
  email       String?
}

model Session {
  id          String   @id
  eventId     String
  title       String
  description String?
  startTime   DateTime
  endTime     DateTime
  room        String?
  speakerId   String?  // relasi ke Speaker
  track       String?  // "Track A", "Workshop", "Keynote"
  capacity    Int?
}

model TicketType {
  id          String   @id
  eventId     String
  name        String   // "Early Bird", "Regular", "VIP"
  price       Decimal
  currency    String   @default("IDR")
  quota       Int?
  description String?
  saleStart   DateTime?
  saleEnd     DateTime?
}

// === TRAVEL/GATHERING ===
model ItineraryItem {
  id          String   @id
  eventId     String
  day         Int      // hari ke-1, ke-2, ...
  time        DateTime
  title       String   // "City Tour", "Check-out"
  location    String?
  notes       String?
  transportMode String? // "Bus", "Pesawat", "Jalan kaki"
}

// === UMUM (multi-kategori) ===
model Certificate {
  id            String   @id
  eventId       String
  participantId String
  templateUrl   String?
  issuedAt      DateTime?
  code          String   @unique // untuk verifikasi
}

model Gallery {
  id          String   @id
  eventId     String
  url         String
  caption     String?
  uploadedAt  DateTime
}
```

### 4.3 Pemetaan kategori → modul/tabel

| Tabel | Sport | Festival | MICE | Travel |
|---|:---:|:---:|:---:|:---:|
| Event, EventConfig, Schedule | ✅ | ✅ | ✅ | ✅ |
| Participant, Contingent | ✅ (atlet) | ✅ (pengunjung) | ✅ (attendee) | ✅ (peserta) |
| Division, Match, Medal, Venue | ✅ | — | — | — |
| Tenant, StageProgram | — | ✅ | — | — |
| Speaker, Session, TicketType | — | — | ✅ | — |
| ItineraryItem | — | — | — | ✅ |
| Gallery, Certificate | opsional | opsional | opsional | opsional |

---

## 5. Implikasi Frontend

### 5.1 Event Detail Page — render kondisional

Halaman `/events/[slug]` akan render section sesuai `event.category` dan `event.modules`:

```
if category == SPORT:
  render [Overview, Divisi, Kontingen, Bracket, Jadwal, Klasemen]
if category == FESTIVAL:
  render [Overview, Tenant Directory, Program Panggung, Peta Lokasi, Info Tiket]
if category == MICE:
  render [Overview, Pembicara, Sesi/Agenda, Tiket, Sertifikat]
if category == OTHER (Travel):
  render [Overview, Itinerary, Info Peserta, Logistik]

// Tambahan (kalau di-aktifkan di modules):
if modules.gallery: render [Galeri Foto]
if modules.livestream: render [Link Live]
```

### 5.2 Admin Panel — per kategori

Setiap kategori punya **sub-menu admin sendiri**:
- Sport: Participants, Matches/Bracket, Standings, Contingents
- Festival: Tenants, Stage Program, Visitors
- MICE: Speakers, Sessions, Tickets, Attendees
- Travel: Itinerary, Participants, Logistics

Yang umum (Schedule, Settings, Gallery) muncul untuk semua kategori.

### 5.3 Registration form — adaptif

Form pendaftaran `/events/[slug]/register` menyesuaikan kategori:
- **Sport:** nama atlet, gender, tanggal lahir, **pilih divisi + kontingen**
- **Festival:** nama pengunjung, jumlah orang, **pilih tiket** (kalau ada)
- **MICE:** nama attendee, email, jabatan, **pilih jenis tiket**
- **Travel:** nama peserta, KTP/passport, kontak darurat, **info kamar**

---

## 6. Implementasi Bertahap

### Fase A — Fondasi Multi-Kategori (prioritas sekarang)
1. Ubah schema: tambah `EventCategory`, `modules`, tabel baru (Tenant, Speaker, Session, TicketType, ItineraryItem)
2. Generalisasi Participant (divisionId/contingentId jadi nullable)
3. Update Event Detail Page untuk render kondisional per kategori
4. Update Registration form agar adaptif

### Fase B — Event Creation Wizard (5 langkah)
1. Admin UI wizard step 1-5 (server action multi-step)
2. Sport template: auto-generate Division dari konfigurasi
3. Validasi per step
4. Save as draft → publish

### Fase C — Modul Festival (Tenant + Stage Program)
1. Admin CRUD tenant
2. Public Tenant Directory di detail event
3. Admin CRUD Stage Program → tampil di detail event

### Fase D — Modul MICE (Speaker + Session + Ticket)
1. Admin CRUD speaker & session
2. Public agenda dengan filter track
3. Ticket type + (mock) reservation flow
4. Certificate generation (basic)

### Fase E — Modul Travel (Itinerary)
1. Admin CRUD itinerary
2. Public itinerary view per hari

### Fase F — Modul tambahan (cross-category)
1. Gallery upload
2. Livestream link
3. Certificate template & batch issue

---

## 7. Pertanyaan Terbuka

1. **Upload file** (poster, logo tenant, foto pembicara, dokumen KTP) — pakai storage apa? VPS local disk, S3-compatible (Cloudflare R2 / MinIO), atau upload ke Cloudinary? Untuk prototype bisa local disk di `/var/www/eo-sanasini/uploads/` dengan serving via Next.js.
   ✅ **KEPUTUSAN: VPS local disk dulu** (`/var/www/eo-sanasini/uploads/`)
2. **Payment gateway** untuk tiket MICE — apakah perlu sekarang atau mock dulu? (Midtrans/Xendit untuk Indonesia).
   ✅ **KEPUTUSAN: Mock dulu** (tanpa payment real, status ticket reservation = pending)
3. **Multi-currency** — hanya IDR, atau perlu USD untuk event internasional?
   ✅ **KEPUTUSAN: IDR saja untuk awal** (schema tetap punya field `currency` untuk fleksibilitas, tapi default `IDR`)
4. **Role admin** — apakah wizard create event bisa dipakai semua admin, atau hanya super-admin EO Sanasini?
   ✅ **KEPUTUSAN: 3-tier role system** (lihat section 7a di bawah)

### 7a. Role System (detail)

| Role | Siapa | Bisa | Tidak bisa |
|---|---|---|---|
| **SUPER_ADMIN** | Programmer (maintainer sistem) | Semua — user & role management, global org settings, create/edit/delete event apapun, system config, lihat semua event | — |
| **ADMIN** | Manajemen EO Sanasini | Create/edit/delete event, assign staf ke event, manage semua content event | User/role management, system config (yang harus di-set super admin) |
| **STAF** | Panitia per-event | Manage **hanya event yang di-assign** ke dia: peserta, pertandingan, jadwal, konten event itu | Create/delete event, akses event lain, manage user |

Implementasi:
- Model `User` + enum `GlobalRole { SUPER_ADMIN, ADMIN, STAF }`
- Junction table `EventStaff` (relasi STAF ↔ Event, dengan field `EventStaffRole` opsional untuk granular per-event: mis. staf A hanya lihat event X)
- Semua admin route diproteksi middleware berbasis session + role
- STAF hanya bisa akses event yang ada di tabel `EventStaff`-nya

---

## 8. Estimasi effort per fase

| Fase | Komponen | Ukuran |
|---|---|---|
| A | Fondasi multi-kategori (schema + generalisasi) | Medium |
| B | Event Creation Wizard 5-step | Large |
| C | Modul Festival | Medium |
| D | Modul MICE | Large (ticket + speaker + session) |
| E | Modul Travel | Small-Medium |
| F | Modul tambahan cross-category | Medium |

Rekomendasi urutan: **A → B → C → D → E → F**, dengan B dan A bisa berjalan bersamaan sebagian.
