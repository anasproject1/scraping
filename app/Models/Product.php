<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'package',
        'link',
        'supplier_name',
        'supplier_country',
        'supplier_type',
        'cas_no',
        'grade',
        'content',
        'port',
        'brand',
        'packaging',
        'price_valid',
        'image_url',
    ];
}
