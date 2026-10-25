# AUDrive - Struktur Folder

Struktur final project AUDrive. Root aplikasi = folder `audrive/`.

## Aplikasi Laravel (root)

```text
audrive/
├── app/
│   ├── Console/Kernel.php
│   ├── Enums/                  # 18 status enum (AiScanStatus, WorkOrderStatus, ...)
│   ├── Exceptions/Handler.php
│   ├── Http/
│   │   ├── Controllers/        # Admin, Api/V1, Auth, Customer, Marketplace, SparePart, Workshop
│   │   ├── Middleware/         # termasuk RoleMiddleware, PermissionMiddleware
│   │   ├── Requests/           # FormRequest per area
│   │   └── Resources/          # API Resource
│   ├── Jobs/                   # ProcessAiScanJob, GeneratePdfJob, dsb.
│   ├── Models/                 # 32 model Eloquent
│   ├── Policies/
│   ├── Providers/
│   ├── Services/               # AiScanService, Ai/, Notification/, Payment/, Pdf/, bisnis lainnya
│   └── Support/                # Trait HasCode, HasSlug, HasUuid
├── bootstrap/app.php + cache/
├── config/                     # ai_service.php, ai.php, app, auth, cache, cors, database, ...
├── database/
│   ├── factories/              # 32 factory
│   ├── migrations/             # 35 migration
│   └── seeders/                # DatabaseSeeder, RolePermissionSeeder, UserSeeder, CarUnitSeeder
├── docker/                     # placeholder (docker-compose.yml, nginx/, php/ — saat ini kosong)
├── public/                     # index.php, .htaccess, build hasil Vite (diabaikan Git)
├── resources/
│   ├── css/                    # app.css, lamborghini.css
│   ├── js/                     # app.js, bootstrap.js
│   └── views/                  # admin, auth, components, customer, layouts, marketplace, pdf, ...
├── routes/                     # web, api, auth, admin*, customer*, marketplace, spare-parts, workshop, console, channels
├── services/                   # (dihapus — berisi stub Python v1.0.0 yang sudah digantikan ai-service/)
├── storage/                    # framework/cache, framework/views, logs, app/public (upload)
├── tests/
│   ├── Feature/                # ApiTest, AuthTest, MarketplaceTest, QAHardeningTest, SupportServicesTest, WorkshopTest
│   ├── Unit/
│   └── TestCase.php
├── ai-service/                 # Program Python — lihat bagian di bawah
└── (root files)
```

File konfigurasi di root:

| File | Fungsi |
|---|---|
| `artisan` | CLI Laravel |
| `composer.json` / `composer.lock` | Dependensi PHP |
| `package.json` / `package-lock.json` | Dependensi Node/Vite |
| `phpunit.xml` | Konfigurasi test (sqlite :memory:) |
| `vite.config.js` | Build asset (laravel-vite-plugin + Tailwind 4) |
| `tailwind.config.js` | Konfigurasi Tailwind |
| `.env` / `.env.example` | Environment aktif / template |
| `.gitignore`, `.gitattributes`, `.editorconfig`, `.npmrc` | Konfigurasi repo & tooling |
| `README.md`, `STRUCTURE.md`, `AUDrive_Context.md`, `QA_CHECKLIST.md`, `AGENTS.md`, `CLAUDE.md` | Dokumentasi |

## Program Python — `ai-service/`

```text
ai-service/
├── app/
│   ├── __init__.py
│   ├── loader.py           # memuat model sesuai MODEL_BACKEND
│   ├── preprocessor.py     # Langkah 1 & 2: validasi kualitas + MFCC/Mel-spectrogram
│   ├── predictor.py        # Langkah 3: inferensi + susun laporan diagnostik
│   └── schemas.py          # Pydantic models (PredictResponse, HealthResponse)
├── models/                 # DROP file model di sini — lihat README.txt
│   ├── README.txt
│   ├── preprocessing/      # scaler.pkl, label_encoder.pkl
│   ├── core/               # model.pth / model.keras
│   ├── universal/          # model.onnx (rekomendasi produksi)
│   └── edge/               # model.tflite
├── config.py               # Konfigurasi terpusat (path, kelas, URGENCY_MAP, threshold)
├── convert_model.py        # Converter model (.pth → .onnx)
├── main.py                 # FastAPI v2.0.0 — endpoint: /predict, /spectrogram/{f}, /health, /classes
├── requirements.txt        # Dependensi Python (venv lokal di .venv/)
├── .env.example            # MODEL_BACKEND, API_HOST, API_PORT, API_SECRET
└── .venv/                  # virtual environment lokal (diabaikan Git)
```

## Integrasi Laravel ↔ Python

- Transport: HTTP (bukan path file). Laravel memanggil `AI_SERVICE_URL` (default `http://localhost:8001`).
- Endpoint Python: `POST /predict`, `GET /spectrogram/{filename}`, `GET /health`, `GET /classes`.
- Autentikasi: header `X-Api-Secret` dibandingkan dengan `API_SECRET` di env Python.
- Format data diPreserve oleh `app/Services/AiScanService.php` + `ai-service/app/schemas.py`.

---

Terakhir diperbarui: 25 Oktober 2026 (setelah konsolidasi struktur — `laravel/` dihapus, root `audrive/` menjadi satu-satunya aplikasi).
