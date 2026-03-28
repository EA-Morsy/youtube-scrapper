<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Playlist extends Model
{
    protected $fillable = [
        'playlist_id',
        'title',
        'description',
        'thumbnail',
        'channel_name',
        'category_id',
        'video_count',
        'duration',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
