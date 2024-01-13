<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trajet extends Model
{
    use HasFactory;
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
