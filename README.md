# EO Sanasini — Event Platform

Platform web untuk **EO Sanasini** — Event Organizer, MICE & Travel Agency (berpengalaman sejak 2009). Mencakup landing page profil, katalog event, dan halaman detail event dengan sistem manajemen kejuaraan olahraga yang **reusable**.

Implementasi pertama: **Sanasini Taekwondo Championship 2026**.

## 🏗️ Tech Stack

| Layer | Tech |
|---|---|
| Framework | Next.js 14 (App Router) |
| Language | TypeScript |
| Styling | Tailwind CSS + custom UI components |
| Database | PostgreSQL 16 |
| ORM | Prisma 6 |
| Runtime | Docker (docker-compose) |

## 📁 Struktur

```
eo-sanasini/
├── docs/
│   └── PRD.md                  # Product Requirements Document
├── frontend/                   # Next.js app
│   ├── prisma/
│   │   ├── schema.prisma       # Event engine data model
│   │   ├── seed.ts             # Seed Taekwondo Championship
│   │   └── migrations/
│   ├── src/
│   │   ├── app/                # Routes (App Router)
│   │   ├── components/         # UI + sections + layout
│   │   └── lib/                # Prisma client, utils, helpers
│   ├── Dockerfile
│   └── package.json
├── docker-compose.yml          # dev stack (app + db)
└── .env                        # env vars (gitignored)
```

## 🚀 Menjalankan

### Prasyarat
- Docker + Docker Compose

### Development
```bash
# 1. Setup env (copy & edit kalau perlu)
cp .env.example .env

# 2. Build & jalankan stack
docker compose up -d --build

# 3. Jalankan migrasi database
docker compose exec app npx prisma migrate dev

# 4. Generate Prisma client
docker compose exec app npx prisma generate

# 5. Seed data (Taekwondo Championship)
docker compose exec app npx tsx prisma/seed.ts
```

Akses: `http://localhost:3001`

### Akses dev live
- App: http://194.233.90.53:3001
- PostgreSQL: `localhost:5433` (user: sanasini, db: eo_sanasini)

## 🏆 Konsep Event Engine

Engine-nya **tidak terikat cabang olahraga** — general dengan konfigurasi spesifik per event. Detail lengkap di [`docs/PRD.md`](docs/PRD.md).

**Format kompetisi:** Full Knockout · Group→Knockout · Round Robin · Scoring · Non-Competitive

**Tipe pendaftaran:** Individual · Team/Kontingen · Hybrid (individu wajib pilih kontingen)

## 📄 License

Proprietary — EO Sanasini.
