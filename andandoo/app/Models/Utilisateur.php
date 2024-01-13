<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    use HasFactory;
    public function avis()
    {
        return $this->hasMany(Avis::class);
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
    public function voiture()
    {
        return $this->belongsTo(Voiture::class);
    }
}
