<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchVideo extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fixture_id',
        'title',
        'video_url',
        'thumbnail_url',
        'duration',
        'type',
        'caption',
        'uploaded_by',
    ];

    /**
     * Get the fixture this video belongs to.
     */
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    /**
     * Get user who uploaded or created this clip.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Check if video is a direct MP4/WebM file.
     */
    public function getIsDirectVideoAttribute(): bool
    {
        $url = strtolower($this->video_url);
        return str_ends_with($url, '.mp4') || str_ends_with($url, '.webm') || str_contains($url, '/storage/');
    }

    /**
     * Format embed URL for YouTube, YouTube Shorts, or Vimeo.
     */
    public function getEmbedUrlAttribute(): string
    {
        $url = trim($this->video_url);

        // YouTube Shorts: https://www.youtube.com/shorts/ID
        if (preg_match('/(?:youtube\.com\/shorts\/|youtu\.be\/|youtube\.com\/watch\?v=)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return 'https://www.youtube-nocookie.com/embed/' . $matches[1] . '?autoplay=1&rel=0';
        }

        // Vimeo: https://vimeo.com/ID
        if (preg_match('/vimeo\.com\/([0-9]+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1] . '?autoplay=1';
        }

        return $url;
    }
}

