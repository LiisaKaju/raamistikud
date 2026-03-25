<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marker extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'description',
        'added',
        'edited',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'added' => 'datetime',
            'edited' => 'datetime',
        ];
    }
}
