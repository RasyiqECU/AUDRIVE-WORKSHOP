# AUDrive — Struktur Folder

Root aplikasi = root repository.

## Aplikasi Laravel

```text
audrive/
├── app/
│   ├── Console/                # Kernel.php, Commands/ (demo:reset, pengingat servis)
│   ├── Enums/                  # 18 enum status
│   ├── Exceptions/
│   ├── Http/                   # Controllers/, Middleware/, Requests/, Resources/
│   ├── Jobs/                   # ProcessAiScanJob, GeneratePdfJob, dan lainnya
│   ├── Models/                 # 39 model
│   ├── Policies/               # 12 policy
│   ├── Providers/
│   ├── Services/               # logika bisnis per domain, Notification/, Payment/, Pdf/
│   └── Support/                # HasCode, HasSlug, HasUuid, Terbilang
├── bootstrap/
├── config/                     # termasuk ai_service.php
├── database/
│   ├── factories/              # 33
│   ├── migrations/             # 54
│   └── seeders/                # lihat di bawah
├── docker/                     # placeholder (belum dipakai)
├── docs/                       # dokumentasi
├── public/                     # index.php, images/ (foto katalog & latar), build/
├── resources/                  # css/, js/, views/
├── routes/                     # web, api, auth, admin, customer, marketplace,
│                               # spare-parts, workshop, channels, console
├── storage/                    # log, cache, app/public (unggahan), app/payment-proofs
├── tests/                      # Pest: Feature/ (35 file), Unit/
└── ai-service/                 # program Python
```

Seeder:

| File | Dipakai oleh |
|---|---|
| `DatabaseSeeder` | `migrate --seed`: memanggil `RolePermissionSeeder`, `UserSeeder`, `SparePartCategorySeeder` |
| `DemoDataSeeder`, `DemoSupportDataSeeder` | `php artisan demo:reset` |
| `CarUnitSeeder`, `SparePartSeeder` | tidak dipanggil otomatis |

File konfigurasi di root: `composer.json`, `package.json`, `phpunit.xml` (test
memakai MySQL `audrive_test`), `vite.config.js`, `tailwind.config.js`,
`.env.example`. `AGENTS.md`, `CLAUDE.md`, dan `.agents/` adalah konfigurasi
tooling coding agent, bukan dokumentasi project.

## Program Python — `ai-service/`

```text
ai-service/
├── app/
│   ├── loader.py           # memuat model sesuai MODEL_BACKEND
│   ├── preprocessor.py     # validasi kualitas dan ekstraksi fitur
│   ├── predictor.py        # inferensi dan laporan diagnostik
│   └── schemas.py          # model Pydantic
├── models/                 # lihat models/README.txt
├── config.py               # path, kelas, urgensi, ambang batas
├── main.py                 # FastAPI: /predict, /spectrogram/{file}, /health, /classes
├── train_yamnet.py         # melatih model klasifikasi
├── train_gate_v3.py        # melatih gate "mesin / bukan mesin"
├── install_hub.sh, wsl_run.sh
├── requirements.txt
└── .env.example            # MODEL_BACKEND, API_HOST, API_PORT, API_SECRET
```

## Integrasi Laravel ↔ Python

HTTP (audio dikirim sebagai multipart), alamat dari `AI_SERVICE_URL`, autentikasi
header `X-Api-Secret` = `API_SECRET`. Sisi Laravel:
`app/Services/AiScanService.php`; sisi Python: `ai-service/app/schemas.py`.

Terakhir diperbarui: 28 November 2026.
