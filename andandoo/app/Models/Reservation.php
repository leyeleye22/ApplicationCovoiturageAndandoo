<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
    public function trajet()
    {
        return $this->belongsTo(Trajet::class);
    }
}
