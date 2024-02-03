<?php

namespace App\Models;

use App\Models\Trajet;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reservation extends Model
{
    use HasFactory;
    protected $fillable=[
        'NombrePlaces'
    ];
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
    public function trajet()
    {
        return $this->belongsTo(Trajet::class);
    }
}
