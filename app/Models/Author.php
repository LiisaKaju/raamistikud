<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Author extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'date_of_birth',
    ];

    // Kui kasutad guarded, siis fillable ei ole vaja
    // protected $guarded = [
    //     'created_at_formated',
    //     'updated_at_formated'
    // ];

    /**
     * Formatted created_at
     */
    protected function createdAtFormated(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->created_at?->diffForHumans()
        );
    }

    /**
     * Formatted updated_at
     */
    protected function updatedAtFormated(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->updated_at?->diffForHumans()
        );
    }
    public function posts() {
        return $this->hasMany(post::class);
    }
}
    

