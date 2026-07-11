# PRD — Platform Event EO Sanasini

| | |
|---|---|
| **Dokumen** | Product Requirements Document (PRD) v1.0 |
| **Klien** | EO Sanasini — Event Organizer, MICE & Travel Agency (sejak 2009) |
| **Referensi brand** | https://www.instagram.com/eosanasini/ |
| **Status** | Draft untuk approval |
| **Tanggal** | 11 Juli 2026 |

---

## 1. Latar Belakang & Tujuan

EO Sanasini adalah event organizer berpengalaman (15+ tahun) yang menangani
event berskala besar seperti Pekan Raya Indonesia dan Festival Olahraga
(Feskul Indonesia). Saat ini pengelolaan event masih dilakukan secara
manual/ad-hoc per event.

**Tujuan:** membangun **platform web** yang berfungsi sebagai:

1. **Showcase profesional** untuk profil EO Sanasini (landing page).
2. **Katalog event** yang sedang & akan datang.
3. **Halaman detail event** interaktif — terutama untuk kejuaraan olahraga
   (jadwal, bracket, klasemen live, hasil medali).
4. **Sistem manajemen event** yang **reusable** — satu engine untuk semua
   jenis event, dengan Taekwondo sebagai implementasi pertama.

**Prinsip desain utama:** _general engine, specific configuration._
Engine-nya tidak terikat cabang olahraga; setiap event hanya mengisi
template konfigurasi.

---

## 2. Brand & Arah Desain

- **Gaya:** Professional & elegan — minimalis, whitespace lega, tipografi
  rapi, palet warna netral + satu warna aksen.
- **Kesan:** berpengalaman, terpercaya, modern. Mencerminkan 15+ tahun
  rekam jejak.
- **Fleksibilitas:** landing page & katalog memakai gaya universal (cocok
  untuk MICE, pameran, festival, dll). Aksen sporty baru muncul di halaman
  detail event bertipe olahraga/kejuaraan.
- **Sifat:** mobile-first (mayoritas pengunjung dari Instagram datang via HP),
  fast-loading, SEO-friendly.

> Catatan: palet warna final, logo, dan aset brand resmi akan diambil dari
> brief EO Sanasini. Untuk prototipe dipakai palet netral profesional.

---

## 3. Konsep Inti: Event Engine yang Reusable

### 3.1 Filosofi

Setiap event di platform adalah **instance dari sebuah template**. Template
menentukan: format kompetisi, tipe pendaftaran, kategori, dan cabang lomba.
Engine mengolah konfigurasi tersebut menjadi: jadwal, bracket, klasemen,
dan papan medali — **tanpa hard-code per cabang olahraga**.

Contoh reuse di masa depan:
- Kejuaraan Taekwondo → _Kyorugi + Poomsae_, kelas berat badan
- Liga Futsal → _Round Robin_, registrasi tim
- Turnamen Badminton → _Knockout_, kelas ganda/campuran
- Festival Seni → bukan kompetisi (event non-kompetitif, tanpa klasemen)

Semua pakai engine yang sama, hanya beda konfigurasi.

### 3.2 Format Kompetisi (dipilih per event)

| Format | Mekanisme | Cocok untuk |
|---|---|---|
| **Full Knockout** | Single elimination. Kalah = gugur. | Slot waktu pendek, peserta banyak |
| **Group → Knockout** | Babak grup (round-robin kecil) → lolos ke bracket knockout. Gaya Piala Dunia. | Fair, banyak pertandingan — **paling populer** |
| **Full Liga (Round Robin)** | Semua lawan semua, poin akumulatif. | Liga musiman/berkelanjutan |
| **Scoring / Performance** | Penilaian wasit/juri, ranking by skor. | Poomsae, seni, tari, gymnastics |
| **Non-kompetitif** | Tanpa klasemen (pameran, festival, seminar). | Event non-lomba |

Satu event bisa mengkombinasikan beberapa format per cabangnya (mis.
Kyorugi = Group→Knockout, Poomsae = Scoring).

### 3.3 Tipe Pendaftaran (dipilih per event)

| Tipe | Mekanisme | Klasemen yang dihasilkan |
|---|---|---|
| **Individual** | Atlet daftar sendiri, tidak terikat kontingen. | Hanya klasemen individu |
| **Team / Kontingen** | Kontingen (negara/provinsi/perguruan) mendaftarkan N peserta sekaligus via admin kontingen. | Klasemen kontingen (Best Contingent) |
| **Hybrid** ⭐ | Atlet daftar sebagai individu **tetapi wajib memilih kontingen** (mis. perguruan/provinsi/negara). | **Klasemen individu + klasemen kontingen** sekaligus. Paling sering dipakai di kejuaraan nasional. |

### 3.4 Dimensi Peserta (umum untuk semua cabang)

Setiap peserta diklasifikasikan dengan dimensi berikut:

- **Kategori umur** — Pre-teen, Cadet, Junior, Senior, Master (configurable per event)
- **Gender** — Putra / Putri / Mixed
- **Kelas** — tergantung cabang. Taekwondo = kelas berat; lainnya =
  kategori relevan (mis. tingkat sabuk, divisi).
- **Cabang lomba** — Taekwondo: Kyorugi (pertandingan) & Poomsae (jurus).
  Tiap cabang bisa punya medali sendiri.

Kombinasi kategori+gender+kelas+cabang = satu **pool/class** pertandingan.

### 3.5 Output Hasil yang Ditampilkan

- 🥇 **Klasemen Individu** — papan medali per pool/class
- 🏆 **Klasemen Kontingen** — akumulasi medali, auto-rank
  (Gold > Silver > Bronze). Juara umum = "Overall Champion / Best Contingent"
- 📊 **Dashboard statistik** — jumlah peserta, kontingen, pertandingan
  selesai, persentase progress event

---

## 4. Implementasi Referensi: Kejuaraan Taekwondo

Event pertama, sekaligus validasi bahwa engine cukup general. Konfigurasi:

| Aspek | Nilai |
|---|---|
| Nama (contoh) | "Sanasini Taekwondo Championship 2026" |
| Skala | Nasional / multi-kontingen |
| Tipe pendaftaran | **Hybrid** — atlet individu wajib pilih kontingen |
| Kontingen | Perguruan / Provinsi / Negara |
| Cabang | **Kyorugi** (pertandingan) + **Poomsae** (jurus) |
| Format Kyorugi | **Group → Knockout** |
| Format Poomsae | **Scoring** (penilaian wasit, ranking by skor) |
| Medali Kyorugi | Gold, Silver, 2× Bronze (standar World Taekwondo — 2 semifinalis kalah dapat bronze) |
| Kategori umur | Pre-teen, Cadet, Junior, Senior |
| Kelas | Kelas berat badan per kategori+gender (mengikuti tabel resmi WT) |

**Skenario klasemen (validasi):**
1. Tiap pool Kyorugi menghasilkan 1G + 1S + 2B.
2. Poomsae menghasilkan 1G + 1S + 2B per kategori.
3. Medali diakumulasi ke kontingen masing-masing atlet.
4. Kontingen dengan total Gold terbanyak (tie-break: Silver, lalu Bronze)
   jadi **Overall Champion**.

---

## 5. Halaman & Navigasi (Frontend)

### 5.1 Landing Page (public — `/`)
- Hero: tagline EO Sanasini, CTA "Lihat Event" / "Hubungi Kami"
- Sekilas profil: 15+ tahun pengalaman, portofolio (Pekan Raya, Feskul)
- Layanan: Event Organizer, MICE, Travel Agency
- Statistik: jumlah event, total peserta, tahun pengalaman
- Event unggulan / mendatang (preview card)
- Testimoni (opsional)
- Footer: kontak, sosmed, link

### 5.2 Katalog Event (`/events`)
- Grid/list event: upcoming, ongoing, past
- Filter: by tipe (kejuaraan/MICE/festival), by status
- Card: poster, nama, tanggal, lokasi, status badge, tipe

### 5.3 Detail Event (`/events/[slug]`)
Bervariasi sesuai tipe event. Untuk kejuaraan olahraga:

- **Overview** — info umum, lokasi (peta), tanggal, kontak panitia
- **Kategori & Cabang** — daftar pool/class yang dipertandingkan
- **Jadwal** — timeline pertandingan per hari/venue
- **Bracket / Pool** — visualisasi Group→Knockout (interaktif)
- **Klasemen Individu** — tabel medali per pool
- **Klasemen Kontingen** — tabel Overall Champion
- **Statistik live** — progress bar event
- **Galeri / Berita** (opsional)
- **Cara daftar** — link ke pendaftaran (jika dibuka)

### 5.4 Pendaftaran (`/events/[slug]/register`) — fase selanjutnya
Form: data atlet, pilih kontingen, pilih kategori/gender/kelas, unggah
dokumen (KTP, bukti sabuk/sertifikat). Verifikasi email.

### 5.5 Area Admin (fase selanjutnya)
- **Super Admin (EO Sanasini)** — buat event, atur template, kelola user
- **Admin Kontingen** — daftarkan atlet kontingennya, lihat status
- **Admin Pertandingan** — input skor/hasil pertandingan, generate bracket

---

## 6. Data Model (Entitas Inti)

```
Organization (EO Sanasini) — 1
 └── Events[] — banyak event (Taekwondo, Futsal, Festival, ...)
      ├── EventConfig — format, tipe registrasi, kategori, cabang
      ├── Contingents[] — kontingen/negara/perguruan
      ├── Divisions[] — pool/class (kategori×gender×kelas×cabang)
      │     └── Participants[] — atlet per pool
      ├── Matches[] — pertandingan (bracket/round)
      ├── Standings — dihitung: individu + kontingen
      ├── Schedule[] — jadwal per hari/venue
      └── Venues[] — lokasi/arena
```

**Entitas kunci:**

- **Event** — id, slug, nama, tipe (championship/league/festival/mice),
  tanggal, lokasi, poster, deskripsi, status (draft/upcoming/ongoing/completed)
- **EventConfig** — formatKompetisi, tipeRegistrasi, kategoriUmur[],
  cabangLomba[], tabelKelas, sistemMedali (1 atau 2 bronze)
- **Contingent** — id, eventId, nama, tipe (perguruan/provinsi/negara),
  logo, kontak
- **Division (pool)** — id, eventId, cabang, kategoriUmur, gender, kelas,
  format (knockout/group/scoring)
- **Participant** — id, divisionId, contingentId, nama, gender, tanggalLahir,
  dokumen, status (pending/approved/rejected), seed
- **Match** — id, divisionId, round, bracketPosition, pesertaA, pesertaB,
  skor, pemenang, status (scheduled/ongoing/completed), venue, jadwal
- **Medal** — participantId, contingentId, divisionId, jenis (G/S/B), cabang
- **Standing** (derived) — contingentId, totalG, totalS, totalB, rank

Skema lengkap (SQL schema / Prisma) akan dibuat di fase implementasi.

---

## 7. Stack Teknologi

| Lapisan | Teknologi | Alasan |
|---|---|---|
| **Framework** | Next.js 14 (App Router) | Fullstack, SSR/SSG, SEO, routing, API routes dalam satu repo |
| **Bahasa** | TypeScript | Type safety, reusable model event engine |
| **Styling** | Tailwind CSS + komponen UI (shadcn/ui) | Professional, konsisten, cepat, tema terpusat |
| **Database** | PostgreSQL | Relational, cocok untuk struktur event/kontingen/klasemen |
| **ORM** | Prisma | Type-safe schema, migrasi, sejalan dengan TS |
| **Auth** | NextAuth.js (Auth.js) | Multi-role: super admin, admin kontingen, admin pertandingan |
| **Validasi** | Zod | Schema validasi dipakai client & server |
| **State/Data** | TanStack Query + Server Components | Fetch, cache, optimistic update untuk klasemen live |
| **Visualisasi bracket** | react-brackets / custom SVG | Render Group→Knockout |
| **Deploy** | Docker (dev) → Vercel/VM (prod) | Konsisten lintas environment |

---

## 8. Lingkup Prototipe (yang akan dibangun setelah PRD ini disetujui)

Full prototype yang bisa diklik end-to-end, dengan **mock backend**
(PostgreSQL di Docker + data seed Taekwondo). Bukan cuma mockup statis.

**Fungsi prototipe:**
1. Landing page EO Sanasini (lengkap, profesional)
2. Katalog event dengan filter
3. Detail event Taekwondo:
   - Overview, kategori, jadwal
   - Bracket Group→Knockout (Kyorugi) dengan data dummy
   - Hasil Poomsae (scoring)
   - Klasemen individu + klasemen kontingen (auto-dihitung dari data)
   - Statistik live
4. Admin sederhana:
   - Input hasil pertandingan → klasemen auto-update
   - Lihat kontingen & peserta
5. Flow pendaftaran (mock, tanpa payment)

**Di luar prototipe (fase berikutnya):** payment gateway, notifikasi email,
aplikasi mobile, multi-event concurrent production, export sertifikat.

---

## 9. Roadmap

| Fase | Isi |
|---|---|
| **0 — PRD** (dokumen ini) | Konsep & spesifikasi. ✅ setelah disetujui |
| **1 — Setup** | Repo, Docker dev env, Next.js + DB + seed Taekwondo |
| **2 — Landing & Katalog** | Landing EO Sanasini + halaman event list |
| **3 — Detail Event (read)** | Overview, jadwal, kategori |
| **4 — Engine Kompetisi** | Bracket, scoring, perhitungan klasemen |
| **5 — Admin & Pendaftaran** | Input hasil, registrasi peserta |
| **6 — Polish** | Responsive, SEO, performa, deploy |

---

## 10. Pertanyaan Terbuka

1. **Aset brand** — apakah ada logo, palet warna resmi, dan brief dari EO
   Sanasini? Untuk prototipe saya pakai placeholder profesional.
2. **Nama event Taekwondo** yang sebenarnya? (sementara pakai nama contoh)
3. **Tabel kelas berat** — apakah mengikuti standar World Taekwondo (WT)
   penuh, atau subset saja untuk prototipe?
4. **Kontingen** — tipenya perguruan, provinsi, atau negara? (bisa
   campuran, cukup ditentukan saat buat event)
5. **Apakah perlu multi-bahasa** (ID/EN)?

---

## 11. Ringkasan Keputusan

| Item | Keputusan |
|---|---|
| Stack | Next.js + TypeScript + PostgreSQL + Prisma + Tailwind |
| Dev environment | Docker (docker-compose) |
| Scope sekarang | PRD dulu, lalu full prototype dengan mock backend |
| Gaya | Professional & elegan, aksen sporty hanya di event olahraga |
| Event engine | General/reusable, Taekwondo = implementasi pertama |
| Format (Taekwondo) | Group→Knockout (Kyorugi) + Scoring (Poomsae) |
| Tipe pendaftaran | Hybrid (individu wajib pilih kontingen) |
| Klasemen | Individu + Kontingen (Overall Champion) |
