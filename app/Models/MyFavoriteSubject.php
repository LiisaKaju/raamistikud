<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MyFavoriteSubject extends Model
{
    use HasFactory;

    protected $table = 'my_favorite_subject';

    protected $fillable = [
        'user_id',
        'title',
        'image',
        'description',
        'location',
        'difficulty',
        'distance_km',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
