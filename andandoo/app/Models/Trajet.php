<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trajet extends Model
{
    use HasFactory;
    protected $fillable=[
        'LieuDepart',
        'LieuArrivee',
        'DateDepart',
        'HeureD',
        'Prix',
        'utilisateur_id'
    ];
    public function voiture()
    {
        return $this->belongsTo(Voiture::class);
    }
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
