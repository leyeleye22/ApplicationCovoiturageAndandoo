<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voiture extends Model
{
    use HasFactory;
    protected $fillable = [
        'ImageVoitures',
        'Descriptions',
        'NbrPlaces'
    ];
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
    public function trajet()
    {
        return $this->hasMany(Trajet::class);
    }
}
