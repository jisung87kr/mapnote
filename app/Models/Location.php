<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
