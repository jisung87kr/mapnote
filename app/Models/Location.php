<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address_name',
        'category_group_code',
        'category_group_name',
        'category_name',
        'distance',
        'map_id',
        'phone',
        'place_name',
        'place_url',
        'road_address_name',
        'lat',
        'lng',
        'place_id',
        'memo'
    ];

    protected $appends = ['x', 'y'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function x(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['lng'],
        );
    }

    protected function y(): Attribute
    {
        return Attribute::make(
            get: fn ($value, $attributes) => $attributes['lat'],
        );
    }
}
