<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'offer_name',
        'client_company',
        'client_name',
        'client_street',
        'client_city',
        'client_post_code',
        'client_country',
        'description',
        'expiration_date',
        'offer_total_price',
        'offer_currency_name',
        'price_country_id',
        'offer_lead_time',
        'client_nip',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country() {
        return $this->belongsTo(Country::class, 'price_country_id');
    }
}
