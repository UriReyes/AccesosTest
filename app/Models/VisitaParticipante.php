<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitaParticipante extends Model
{
    use HasFactory;
    protected $fillable = [
        'visita_id',
        'name',
        'document',
        'status', // por ejemplo: 'pendiente', 'aprobado', 'rechazado'
    ];

    public function visita()
    {
        return $this->belongsTo(Visita::class);
    }
}
