# EO Sanasini — Event Platform

Platform web untuk **EO Sanasini** — Event Organizer, MICE & Travel Agency (berpengalaman sejak 2009). Multi-category event engine: kejuaraan olahraga, festival, MICE, dan travel/gathering.

## 🏗️ Tech Stack

| Layer | Tech | Versi |
|---|---|---|
| Backend | Laravel + Filament v4 | PHP 8.5 / Laravel 13 |
| Frontend | Nuxt + Vue 3 | Nuxt 4 |
| Database | PostgreSQL | 16 (Docker) |
| Process Manager | Supervisor | (sama dgn MES) |

> **Catatan:** Stack ini dipilih agar konsisten dengan project MES di VPS yang sama (Laravel + Nuxt). Versi sebelumnya pakai Next.js — diarsipkan di branch `archive-nextjs`.

## 📁 Struktur Repo

```
eo-sanasini/
├── backend/                      # Laravel 13 + Filament v4
│   ├── app/Models/               # 19 Eloquent models (event engine)
│   ├── app/Providers/Filament/   # AdminPanelProvider
│   ├── database/migrations/      # 7 migration files
│   ├── database/seeders/         # AdminUser, OrganizationEvent
│   ├── routes/api.php            # Public API (/api/events, /api/events/{slug})
│   └── bootstrap/app.php
├── frontend/                     # Nuxt 4 + Vue 3 + Tailwind
│   ├── app/components/{layout,sections}/
│   ├── app/pages/                # index, events/index, events/[slug]
│   ├── app/layouts/
│   ├── app/assets/css/main.css
│   ├── nuxt.config.ts
│   └── tailwind.config.js
├── deploy/
│   └── supervisor-eo-sanasini.conf
├── docs/
│   ├── PRD.md                    # Product Requirements Document
│   ├── ARCHITECTURE.md           # Bible project, all decisions
│   └── PLAN-event-creation-flow.md
└── README.md
```

## 🚀 Akses

| Service | URL | Kredensial |
|---|---|---|
| Public web (Nuxt) | http://194.233.90.53:3001 | — |
| Admin panel (Filament) | http://194.233.90.53:8001/admin | lihat seed |
| API (Laravel) | http://194.233.90.53:8001/api/* | — |
| PostgreSQL | localhost:5433 | sanasini / sanasini_dev_2026 |

**Login Admin:**
- Super Admin: `superadmin@sanasini.id` / `SuperAdmin2026!`
- Admin: `admin@sanasini.id` / `AdminSanasini2026!`

## 🎭 Multi-Category Event Engine

Engine-nya **tidak terikat jenis event**. Setiap event punya `category` yang menentukan modul yang relevan:

| Kategori | Modul | Contoh |
|---|---|---|
| **SPORT** | Division, Match, Bracket, Medal, Standings | Taekwondo, Futsal |
| **FESTIVAL** | Tenant, StageProgram | Pekan Raya, Festival Seni |
| **MICE** | Speaker, Session, TicketType, Certificate | Seminar, Konferensi |
| **OTHER** | ItineraryItem, Participant | Travel, Gathering |

Detail lengkap: [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md)

## 🛠️ Development

Setup dilakukan via SSH ke VPS. Karena dev environment langsung di server, ikuti pola:

```bash
# Restart Laravel (via supervisor)
supervisorctl restart eo-sanasini-laravel

# Restart Nuxt
supervisorctl restart eo-sanasini-nuxt

# Run migration
cd /var/www/eo-sanasini/backend && php artisan migrate

# Run seeder
cd /var/www/eo-sanasini/backend && php artisan db:seed
```

## 📄 License

Proprietary — EO Sanasini.
