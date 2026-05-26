<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UtilityBannersOffer extends Model
{
    use HasFactory;

    protected $table = 'utility_banners_offers';

    protected $fillable = [
        'title',
        'sub_title',
        'image',
        'hyperlink',
        'type',
        'merchant_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Optional: constants for type values
    const TYPE_BANNER = 'BANNER';
    const TYPE_OFFER = 'OFFER';
}