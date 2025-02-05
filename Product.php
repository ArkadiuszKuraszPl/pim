<?php

namespace App\Models;

use App\Models\Shape;
use App\Models\Smell;
use App\Models\StandId;
use App\Models\GlassType;
use App\Models\ProductLine;
use App\Models\ProductType;
use App\Models\DecorationNo;
use App\Models\DecorationName;
use App\Models\DecorationType;
use App\Models\PlatformAccount;
use App\Models\ProductDescription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    // Pola, które mogą być masowo wypełniane (mass assignable)
    protected $fillable = [
        'name',
        'sku',
        'ean',
        'line_id',
        'type_id',
        'category_id',
        'parent_category_id',
        'position_no',
        'status',
        'manufactured_product',
        'product_sold',
        'country_id',
        'customs_code',
        'customs_code_description',
        'height',
        'width',
        'depth',
        'capacity',
        'volume',
        'glass_type_id',
        'shapes_id',
        'smell_id',
        'product_sales_season',
        'color_id',
        'decoration_number_id',
        'decoration_name_id',
        'decoration_type_id',
        'decoration_description',
        'idfinish_id',
        'finish_number_id',
        'finish_name_id',
        'finish_type_id',
        'finish_description',
        'idstand_id',
        'stand_number_id',
        'stand_name_id',
        'stand_type_id',
        'stand_description',
        'hand_made',
        'machine_made',
        'artist_cuts',
        'decal',
        'gold',
        'platinum',
        'metalized',
        'sandblasted_partial_painted',
        'gift_wrapping',
        'possibility_of_customization',
        'product_main_image',
        'product_three_d',
        'producer_id',
        'product_safety',
    ];

    // Relacja z modelem Country (obszar)
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Relacja do linii produktów (product_lines)
    public function line()
    {
        return $this->belongsTo(ProductLine::class);
    }

    // Relacja do typu produktów (product_types)
    public function type()
    {
        return $this->belongsTo(ProductType::class);
    }

    // Relacja do kategorii produktów (product_categories)
    public function category()
    {
        return $this->belongsTo(ProductCategorie::class, 'category_id', 'id');
    }

    // Relacja do nadrzędnej kategorii produktów (product_categories)
    public function parentCategory()
    {
        return $this->belongsTo(ProductCategorie::class, 'parent_category_id', 'id');
    }

    // Relacja do rodzaju szkła (glass_types)
    public function glassType()
    {
        return $this->belongsTo(GlassType::class);
    }

    // Relacja do kształtów (shapes)
    public function shape()
    {
        return $this->belongsTo(Shape::class, 'shapes_id');
    }

    // Relacja do kolorów (colors)
    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    // Relacja do zapachów (smells)
    public function smell()
    {
        return $this->belongsTo(Smell::class);
    }

    // Relacja do numeru ozdób (decoration_nos)
    public function decorationNumber()
    {
        return $this->belongsTo(DecorationNo::class, 'decoration_number_id');
    }

    // Relacja do nazwy ozdób (decoration_names)
    public function decorationName()
    {
        return $this->belongsTo(DecorationName::class, 'decoration_name_id');
    }

    // Relacja do typu ozdób (decoration_types)
    public function decorationType()
    {
        return $this->belongsTo(DecorationType::class, 'decoration_type_id');
    }

    public function prices()
    {
        return $this->hasMany(Price::class);
    }

    public function packagings()
    {
        return $this->hasMany(Packaging::class);
    }

    public function additionalCodes()
    {
        return $this->hasMany(AdditionalCode::class);
    }

    public function descriptions()
    {
        return $this->hasMany(ProductDescription::class);
    }

    public function additionalImages()
    {
        return $this->hasMany(ProductImage::class);
    }

    public function variants()
    {
        return $this->hasMany(Variant::class);
    }

    public function finishes()
    {
        return $this->hasMany(ProductFinish::class);
    }



    public function standId()
    {
        return $this->belongsTo(StandId::class, 'idstand_id');
    }

    public function standNo()
    {
        return $this->belongsTo(StandNo::class);
    }

    public function standName()
    {
        return $this->belongsTo(StandName::class);
    }

    public function platformAccounts()
    {
        return $this->belongsToMany(PlatformAccount::class, 'product_on_platforms', 'product_id', 'platform_account_id')
                    ->withPivot('url')
                    ->withTimestamps();
    }

    public function producer() {
        return $this->belongsTo(Producer::class);
    }
}
