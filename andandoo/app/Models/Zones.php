<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zones extends Model
{
    use HasFactory;
    protected $fillable = [
        'NomZ',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function utilisateur()
    {
        return $this->hasMany(Utilisateur::class);
    }
}
