<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'post_id',
        'content',
    ];


    protected $appends = [
        'created_at_formated',
        'updated_at_formated'
    ];
     


    protected function createdAtFormated(): Attribute
    {
        return Attribute::make(
            get: fn()=>$this->updated_at?->diffForHumans()
        );
    }

    protected function updatedAtFormated(): Attribute
    {
        return Attribute::make(
            get: fn()=>$this->updated_at?->diffForHumans()
        );
    }

    public function comments(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }


}
