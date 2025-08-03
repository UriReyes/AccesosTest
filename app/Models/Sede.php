<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    protected $fillable = ['name'];

    public function visitas()
    {
        return $this->hasMany(Visita::class);
    }
}
