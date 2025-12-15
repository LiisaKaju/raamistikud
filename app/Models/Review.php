<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;
    use HasFormattedDate;

    protected $fillable = [
        'id',
        'customer_name',
        'comment',
        'rating',
        
    ];

    protected $appends = [
        'created_at_formated',
        'updated_at_formated'
    ];
}
