<?php

namespace App\Models;

use App\Models\Country;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'password',
        'parent_id',
        'country_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    public function getAllDescendants()
    {
        // Pobieramy bezpośrednich podrzędnych
        $descendants = $this->children;

        // Rekurencyjnie pobieramy podrzędnych każdego dziecka
        foreach ($this->children as $child) {
            $descendants = $descendants->merge($child->getAllDescendants());
        }

        return $descendants;
    }

    // Relacja z modelem Country (obszar)
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
