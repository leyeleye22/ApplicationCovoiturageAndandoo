<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Utilisateur extends Authenticatable implements JWTSubject
{

    use HasApiTokens, HasFactory, Notifiable;
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected $fillable = [
        'Nom',
        'Prenom',
        'Email',
        'Telephone',
        'ImageProfile',
        'PermisConduire',
        'role',
        'zone_id',
        'TemporaryBlock',
        'PermanentBlock',
        'password'
    ];
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
        return $this->hasOne(Voiture::class);
    }
}
