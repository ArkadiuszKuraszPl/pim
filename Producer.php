<?php

namespace App\Models;

use App\Models\Country;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Producer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'street',
        'house_number',
        'local_number',
        'postal_code',
        'city',
        'country_id',
    ];

    public function country() {
        return $this->belongsTo(Country::class);
    }

    public function responsibleEntities()
    {
        return $this->hasMany(ResponsibleEntity::class, 're_producer_id');
    }

}
