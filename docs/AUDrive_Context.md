# AUDrive — Konteks Project

Cara menjalankan: [`README.md`](README.md). Role dan alur:
[`ROLES_MODUL_FITUR.md`](ROLES_MODUL_FITUR.md). Struktur folder:
[`STRUCTURE.md`](STRUCTURE.md).

## 1. Apa itu AUDrive

Aplikasi manajemen **dealership & workshop otomotif**:

| Modul | Fungsi |
|---|---|
| Marketplace | Katalog mobil, booking fee, penawaran, test drive, titip jual |
| Workshop | Booking, check-in, work order, konfirmasi part, QC, klaim garansi |
| Spare Part & POS | Toko online, stok terkunci, kasir, purchase order |
| Pembayaran | Kasir atau transfer dengan bukti yang diverifikasi manual (tanpa payment gateway) |
| Customer Portal | Kendaraan, pesanan, invoice, garansi, poin, voucher, tiket |
| Admin Panel | Dashboard, approval, laporan, audit log, akses 8 role |
| AI Scan | Diagnostik suara mesin lewat microservice Python |

## 2. Stack & arsitektur

```
┌──────────────────────────┐     HTTP (multipart)      ┌──────────────────────────┐
│ Laravel 13 (PHP ≥ 8.4.1) │ ────────────────────────▶ │ ai-service (FastAPI)     │
│ MySQL, Sanctum, Pest     │  POST /predict            │ Python 3.12              │
│ Blade, Tailwind 4,       │  GET  /spectrogram/{file} │ ONNX Runtime (YAMNet)    │
│ Alpine, Vite, dompdf     │  GET  /health             │ librosa, matplotlib      │
│ Queue: database          │                           │ port 8001                │
└──────────────────────────┘                           └──────────────────────────┘
```

## 3. Workflow AI Scan

1. Customer mengunggah rekaman (WAV/MP3/M4A, maks 20 MB). Kualitas buruk (skor
   di bawah 0,40) → diminta merekam ulang.
2. Ekstraksi fitur: waveform 5 detik; `yamnet.onnx` menghasilkan embedding.
3. Gate `engine_gate.onnx` memeriksa "mesin atau bukan", lalu `model.onnx`
   memberi kelas, confidence, 3 teratas, urgensi, rekomendasi, dan spectrogram.
   Confidence di bawah 0,60 → perlu review manual.
4. Kepala Mekanik atau Super Admin mereview di `/admin/ai-scans`.

Kelas (9): `bad ignition`, `dead battery`, `knocking`, `low oil`, `misfire`,
`normal`, `not an engine`, `power steering`, `serpentine belt`.

## 4. Job & queue

Unggah → `AiScan` (status `pending`) → `ProcessAiScanJob` → queue `database` →
`php artisan queue:work` → `AiScanService` memanggil Python → hasil disimpan.
Tanpa worker atau AI service yang berjalan, scan tidak akan selesai.

## 5. Environment variables kunci

| Variabel | Sisi | Fungsi |
|---|---|---|
| `DB_CONNECTION`, `DB_*` | Laravel | `mysql` (contoh bawaan `sqlite` harus diganti) |
| `AI_SERVICE_URL` | Laravel | Alamat AI service (`http://localhost:8001`) |
| `AI_SERVICE_SECRET` | Laravel | Harus sama dengan `API_SECRET` |
| `AI_SERVICE_TIMEOUT`, `AI_SERVICE_RETRY` | Laravel | Batas waktu dan percobaan ulang |
| `API_SECRET` | ai-service | Validasi request dari Laravel |
| `MODEL_BACKEND` | ai-service | `onnx` (dipakai), `dummy` (bawaan contoh), `xgboost`, `pytorch`, `keras` |

## 6. Konvensi kode

- Bahasa Indonesia untuk label, pesan validasi, dan laporan.
- Harga disimpan sebagai integer rupiah; status memakai backed enum.
- Logika bisnis di `app/Services/`; trait bersama di `app/Support/`.
- Format kode dengan Pint; test dengan Pest.
- Driver payment gateway di `app/Services/Payment/` (fake, Midtrans, Xendit) ada
  di kode tetapi **tidak dipakai** alur pembayaran mana pun.

## 7. Status (28 November 2026)

- AI service memakai backend **onnx** (lihat `ai-service/.env`).
- Test Pest: 256 test, 253 lulus, 3 dilewati (lihat [`QA_CHECKLIST.md`](QA_CHECKLIST.md)).
- Docker, CI/CD, PHPStan, dan Horizon belum ada.
