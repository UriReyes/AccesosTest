<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activo extends Model
{
    protected $fillable = ['name', 'serie'];

    public function visitas()
    {
        return $this->belongsToMany(Visita::class);
    }
}
