<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Fixture extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'edition_id',
        'sport_id',
        'team1_id',
        'team2_id',
        'venue_id',
        'match_date',
        'match_time',
        'round',
        'status',
        'stream_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'match_date' => 'date:Y-m-d',
        ];
    }

    /**
     * Get the edition that the fixture belongs to.
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    /**
     * Get the sport that the fixture is for.
     */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * Get the first team in the fixture.
     */
    public function team1(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    /**
     * Get the second team in the fixture.
     */
    public function team2(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    /**
     * Get the venue where the fixture takes place.
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the result associated with the fixture.
     */
    public function result(): HasOne
    {
        return $this->hasOne(Result::class);
    }

    /**
     * Get the match photos uploaded for this fixture.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(MatchPhoto::class);
    }

    /**
     * Get the timeline events for this fixture.
     */
    public function events(): HasMany
    {
        return $this->hasMany(MatchEvent::class)->orderBy('id', 'asc');
    }

    /**
     * Determine if the fixture has a video stream configured.
     */
    public function hasLiveStream(): bool
    {
        return !empty($this->stream_url);
    }

    /**
     * Get the detected streaming platform (youtube, facebook, or generic).
     */
    public function getStreamPlatformAttribute(): ?string
    {
        if (empty($this->stream_url)) {
            return null;
        }

        $url = strtolower($this->stream_url);
        if (str_contains($url, 'youtube.com') || str_contains($url, 'youtu.be')) {
            return 'youtube';
        }
        if (str_contains($url, 'facebook.com') || str_contains($url, 'fb.watch')) {
            return 'facebook';
        }
        return 'generic';
    }

    /**
     * Convert YouTube, Facebook, or generic stream URL into an embeddable iframe URL.
     */
    public function getEmbedStreamUrlAttribute(): ?string
    {
        if (empty($this->stream_url)) {
            return null;
        }

        $url = trim($this->stream_url);

        // 1. YouTube (supports watch?v=, youtu.be/, live/, embed/)
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|live\/))([\w-]{11})/', $url, $matches)) {
            $videoId = $matches[1];
            return "https://www.youtube-nocookie.com/embed/{$videoId}?rel=0&modestbranding=1";
        }

        // 2. Facebook Video / Live Player Embed
        if (str_contains(strtolower($url), 'facebook.com') || str_contains(strtolower($url), 'fb.watch')) {
            $encoded = urlencode($url);
            return "https://www.facebook.com/plugins/video.php?href={$encoded}&show_text=0";
        }

        // 3. Already an embed URL or generic iframe
        return $url;
    }
}

