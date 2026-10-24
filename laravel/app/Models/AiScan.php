<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AiScanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiScan extends Model
{
    use HasFactory;

    protected $fillable = [
        // Core
        'customer_id',
        'customer_vehicle_id',
        'uploaded_by',
        'audio_path',
        'status',

        // AI Result (Langkah 3)
        'predicted_class',
        'class_label',
        'confidence',
        'urgency_level',
        'wave_analysis',
        'quality_score',
        'quality_warning',
        'ai_diagnostic_notes',
        'mechanic_ai_notes',
        'recommendations',
        'spectrogram_path',

        // Manual Review (Langkah 4)
        'technician_review',
        'reviewed_by',
        'reviewed_at',
        'mechanic_decision',
        'mechanic_final_notes',
        'admin_decision',
        'admin_notes',
        'final_diagnosis',
        'final_urgency',
        'is_confirmed',
    ];

    protected function casts(): array
    {
        return [
            'status'          => AiScanStatus::class,
            'confidence'      => 'decimal:4',
            'quality_score'   => 'decimal:3',
            'recommendations' => 'array',
            'wave_analysis'   => 'array',
            'reviewed_at'     => 'datetime',
            'is_confirmed'    => 'boolean',
        ];
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(CustomerVehicle::class, 'customer_vehicle_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ─── Scopes ───────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', AiScanStatus::Pending);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', AiScanStatus::Completed);
    }

    public function scopeNeedsReview($query)
    {
        return $query->where('status', AiScanStatus::ManualReview);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isReviewed(): bool
    {
        return $this->reviewed_at !== null;
    }

    public function isConfirmed(): bool
    {
        return (bool) $this->is_confirmed;
    }

    /**
     * Diagnosa final: pakai hasil manual jika sudah dikonfirmasi, else AI.
     */
    public function getFinalDiagnosisLabel(): string
    {
        return $this->final_diagnosis ?? $this->class_label ?? $this->predicted_class ?? '-';
    }

    /**
     * Urgensi final: pakai hasil manual jika ada, else AI.
     * Jika semua null tapi ada mechanic_decision, derive dari kelas kerusakan.
     */
    public function getEffectiveUrgency(): string
    {
        // Priority: final_urgency (manual override) > urgency_level (AI) > derive dari kelas mekanik/AI
        if (!empty($this->final_urgency)) {
            return $this->final_urgency;
        }

        if (!empty($this->urgency_level)) {
            return $this->urgency_level;
        }

        // Derive dari kelas kerusakan (mekanik atau AI)
        $class = $this->mechanic_decision ?? $this->predicted_class;
        return $this->urgencyFromClass($class);
    }

    private function urgencyFromClass(?string $class): string
    {
        return match ($class) {
            'normal'             => 'safe',
            'exhaust_leak',
            'bearing_alternator' => 'monitor',
            'rod_knock'          => 'emergency',
            'timing_chain_belt',
            'valve_lifter',
            'combustion_misfire' => 'urgent',
            default              => 'monitor',
        };
    }

    public function getUrgencyLabel(): string
    {
        return match ($this->getEffectiveUrgency()) {
            'safe'      => 'Aman',
            'monitor'   => 'Perlu Dipantau',
            'urgent'    => 'Perlu Segera ke Bengkel',
            'emergency' => 'DARURAT',
            default     => 'Tidak Diketahui',
        };
    }

    public function getUrgencyColor(): string
    {
        return match ($this->getEffectiveUrgency()) {
            'safe'      => 'text-verde border-verde',
            'monitor'   => 'text-giallo border-giallo',
            'urgent'    => 'text-arancio border-arancio',
            'emergency' => 'text-danger border-danger',
            default     => 'text-silver-dim border-silver-dim',
        };
    }

    public function getUrgencyIcon(): string
    {
        return match ($this->getEffectiveUrgency()) {
            'safe'      => '✅',
            'monitor'   => '⚠️',
            'urgent'    => '🔶',
            'emergency' => '🚨',
            default     => '❓',
        };
    }

    public function getUrgencyAction(): string
    {
        return match ($this->getEffectiveUrgency()) {
            'safe'      => 'Kondisi mesin terdengar normal. Lanjutkan servis rutin sesuai jadwal.',
            'monitor'   => 'Pantau kondisi mesin dalam 1–2 minggu. Segera ke bengkel jika suara memburuk.',
            'urgent'    => 'Jadwalkan kunjungan ke bengkel dalam 1–3 hari. Hindari perjalanan jauh.',
            'emergency' => 'Hentikan kendaraan dan jangan nyalakan mesin. Hubungi bengkel segera.',
            default     => 'Konsultasikan kondisi kendaraan Anda ke mekanik profesional.',
        };
    }

    public function getStatusLabel(): string
    {
        if ($this->status instanceof AiScanStatus) {
            return $this->status->label();
        }
        return match ((string) $this->status) {
            'pending'       => 'Menunggu',
            'processing'    => 'Diproses',
            'completed'     => 'Selesai',
            'failed'        => 'Gagal',
            'manual_review' => 'Review Manual',
            'reviewed'      => 'Sudah Direview',
            default         => ucfirst((string) $this->status),
        };
    }

    public function getStatusColor(): string
    {
        if ($this->status instanceof AiScanStatus) {
            return $this->status->color();
        }
        return match ((string) $this->status) {
            'completed'     => 'text-verde border-verde',
            'reviewed'      => 'text-teal-400 border-teal-400',
            'processing'    => 'text-blue-400 border-blue-400',
            'pending'       => 'text-giallo border-giallo',
            'manual_review' => 'text-arancio border-arancio',
            'failed'        => 'text-danger border-danger',
            default         => 'text-silver-dim border-silver-dim',
        };
    }
}