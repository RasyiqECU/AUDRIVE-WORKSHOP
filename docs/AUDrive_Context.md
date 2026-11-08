# AUDrive — Konteks Project

> Dokumen referensi utama tentang apa itu AUDrive, arsitekturnya, dan cara
> komponen-komponennya terhubung. Untuk instruksi menjalankan, lihat
> [`docs/README.md`](README.md). Untuk peta struktur folder, lihat
> [`docs/STRUCTURE.md`](STRUCTURE.md).

## 1. Apa itu AUDrive

Aplikasi manajemen **dealership & workshop otomotif** yang menggabungkan:

| Modul | Fungsi |
|---|---|
| **Marketplace** | Katalog mobil, penawaran (offer), test drive, list sales |
| **Workshop** | Booking service, work order, jadwal mekanik, tracking, part usage |
| **Spare Parts + POS** | Katalog komponen, keranjang, checkout, kasir (point of sale) |
| **Customer Portal** | Kendaraan, invoice, riwayat, loyalty points, voucher, tiket support |
| **Admin Panel** | Dashboard, approval, laporan keuangan, audit log, RBAC 7 role |
| **AI Scan** | Diagnostik suara mesin berbasis Machine Learning (microservice Python) |

## 2. Stack & Arsitektur

```
┌───────────────────────────────┐        HTTP (multipart)       ┌──────────────────────────┐
│  Laravel 13  (PHP ≥ 8.4.1)   │ ────────────────────────────▶ │  ai-service (FastAPI)    │
│  MySQL / Sanctum / Pest /    │  POST /predict                 │  Python 3.12             │
│  Vite + Tailwind 4 / dompdf  │  GET  /spectrogram/{filename} │  XGBoost + sklearn       │
│  Queue: database              │  GET  /health                  │  librosa + matplotlib    │
└───────────────────────────────┘        port 8001              └──────────────────────────┘
```

- **Aplikasi utama** (root repo): Laravel 13, PHP ≥ 8.4.1, MySQL, Sanctum (API),
  Pest (test), Vite + Tailwind CSS 4, dompdf (PDF invoice/receipt).
- **Microservice AI** (`ai-service/`): FastAPI v2.0.0; model XGBoost `.joblib`
  + StandardScaler + LabelMap; ekstraksi fitur MFCC/Mel via librosa;
  spectrogram PNG via matplotlib.
- **Komunikasi**: HTTP eksklusif (bukan akses file) — file audio dikirim sebagai
  multipart form ke `POST /predict`, hasil JSON diolah Laravel, spectrogram
  di-download lewat `GET /spectrogram/{filename}`.

## 3. Workflow AI Scan (4 langkah)

1. **Upload & validasi audio** — customer mengunggah rekaman (wav/mp3/m4a,
   maks 20 MB). Python menilai kualitas (durasi, RMS, clipping, SNR).
   Kualitas buruk → `quality_error` → customer diminta rekam ulang.
2. **Ekstraksi fitur** — 40 MFCC (mean+std = 80) + `spectral_rolloff_hz` +
   `zero_crossing_rate` = **82 fitur**, dinormalisasi `StandardScaler`.
3. **Inference** — `XGBClassifier.predict_proba` → kelas teratas, confidence,
   top-3, urgensi (safe/monitor/urgent/emergency), rekomendasi customer &
   catatan mekanik. Mel-spectrogram di-render sebagai bukti visual.
   `confidence < 0.60` → ditandai perlu review manual.
4. **Review manual** — mekanik/admin dapat mengkonfirmasi atau mengoreksi
   diagnosa AI lewat `admin/ai-scans`.

Kelas model (9): `bad_ignition`, `dead_battery`, `low_oil`, `normal_brakes`,
`normal_engine_idle`, `not an engine`, `power_steering`, `serpentine_belt`,
`worn_out_brakes`.

## 4. Alur Job & Queue

Upload → `AiScan` dibuat (status `pending`) → `ProcessAiScanJob::dispatch()` →
queue driver **database** → `php artisan queue:work` memproses →
`AiScanService` memanggil Python → hasil (kelas, urgensi, wave analysis,
rekomendasi, spectrogram) disimpan ke tabel `ai_scans`.

> Tanpa worker berjalan, scan akan berhenti di status `pending`.

## 5. Environment Variables Kunci

| Variabel | Sisi | Fungsi |
|---|---|---|
| `AI_SERVICE_URL` | Laravel `.env` | Base URL Python (`http://localhost:8001`) |
| `AI_SERVICE_SECRET` | Laravel `.env` | Header `X-Api-Secret` — **harus sama** dengan `API_SECRET` |
| `API_SECRET` | `ai-service/.env` | Validasi request dari Laravel |
| `MODEL_BACKEND` | `ai-service/.env` | `xgboost` \| `onnx` \| `pytorch` \| `keras` \| `dummy` |
| `DB_CONNECTION` | `.env` | `mysql` (produksi/lokal) atau `sqlite` (test in-memory) |

## 6. Konvensi Kode

- Bahasa Indonesia untuk semua pesan validasi, label UI, dan konten laporan.
- Semua harga integer IDR; tanggal `Y-m-d`; jam `H:i`; status memakai backed enum.
- Trait bersama di `app/Support/` (`HasCode`, `HasSlug`, `HasUuid`, `Terbilang`).
- Service layer per domain di `app/Services/` (WorkOrder, Pos, Payment, dsb.),
  payment gateway lewat driver manager (fake/midtrans/xendit).

## 7. Status Saat Ini (Nov 2026)

- ✅ AI service berjalan dengan backend **xgboost** (model + preprocessing tervalidasi 82 fitur).
- ✅ Test suite Pest: **83 test — 80 pass, 3 skip** (lihat `QA_CHECKLIST.md`).
- ⬜ Docker: belum diimplementasikan (`docker/docker-compose.yml` masih placeholder).
- ⬜ CI/CD, PHPStan, Horizon: belum ada di repo — lihat roadmap di `QA_CHECKLIST.md`.
