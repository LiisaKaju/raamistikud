<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
   use HasFactory;
    
    protected $fillable = [
        'id',
        'name',
        'description',
        'price',
        
    ];

    protected $appends = [
        'created_at_formated',
        'updated_at_formated'
    ];
}
