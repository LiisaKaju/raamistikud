<?php

namespace App\Models;

use App\Traits\HasFormattedDate;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory, HasFormattedDate;

    protected $fillable = [
        'title',
        'content',
        'author_id',
        'published',
    ];

    protected $casts = [
        'published' => 'boolean',
    ];

    protected $appends = [
        'created_at_formatted',
        'updated_at_formatted',
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
