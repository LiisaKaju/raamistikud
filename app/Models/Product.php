<?php

namespace App\Models;

use App\Traits\HasFormattedDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    use HasFormattedDate;

    protected $fillable = [
        'name',
        'description',
        'price',
        'sku',
        'stock_quantity',
    ];
}
