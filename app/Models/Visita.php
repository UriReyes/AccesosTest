<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Visita extends Model
{
    use HasFactory;

    const TYPE_INTERNA = 'interna';
    const TYPE_EXTERNA = 'externa';

    const STATUS_PENDING    = 'pending';
    const STATUS_AUTHORIZED = 'authorized';
    const STATUS_APPROVED   = 'approved';
    const STATUS_REJECTED   = 'rejected';
    protected $fillable     = [
        'type',
        'visitor_name',
        'visitor_document',
        'time_start',
        'time_end',
        'dates',
        'sede_id',
        'user_id',
        'authorized_by',
        'status',
        'qr_token',
        'qr_used_at',
    ];

    protected $casts = [
        'dates'      => 'array',
        'qr_used_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($visita) {
            $visita->qr_token = (string) Str::uuid();
        });
    }

    public function regenerateQrToken(): void
    {
        $this->qr_token = (string) Str::uuid();
        $this->save();
    }

    public function activos()
    {
        return $this->belongsToMany(Activo::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function participants()
    {
        return $this->hasMany(VisitaParticipante::class);
    }

    public function authorizedBy()
    {
        return $this->belongsTo(User::class, 'authorized_by');
    }

    public function scopeInternas($query)
    {
        return $query->where('type', self::TYPE_INTERNA);
    }

    public function scopeExternas($query)
    {
        return $query->where('type', self::TYPE_EXTERNA);
    }
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeAuthorized($query)
    {
        return $query->where('status', self::STATUS_AUTHORIZED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeQrUsed($query)
    {
        return $query->whereNotNull('qr_used_at');
    }

    public function scopeQrUnused($query)
    {
        return $query->whereNull('qr_used_at');
    }

    /**
     * Marca el QR como usado, guardando la fecha/hora actual.
     */
    public function markQrAsUsed(): void
    {
        $this->qr_used_at = now();
        $this->save();
    }

    /**
     * Verifica si el QR ya fue usado.
     */
    public function isQrUsed(): bool
    {
        return ! is_null($this->qr_used_at);
    }
}
