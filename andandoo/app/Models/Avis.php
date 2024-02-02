<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    use HasFactory;
    protected $fillable=[
        'Contenue',
        'Notation'
    ];
    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class);
    }
}
