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

    /**
     * Get the real Unicode sport emoji corresponding to the sport discipline.
     */
    public function getSportEmojiAttribute(): string
    {
        $name = strtolower($this->sport?->name ?? '');

        if (str_contains($name, 'cricket')) {
            return '🏏';
        }
        if (str_contains($name, 'football') || str_contains($name, 'futsal') || str_contains($name, 'soccer')) {
            return '⚽';
        }
        if (str_contains($name, 'basketball')) {
            return '🏀';
        }
        if (str_contains($name, 'volleyball')) {
            return '🏐';
        }
        if (str_contains($name, 'badminton')) {
            return '🏸';
        }
        if (str_contains($name, 'tennis') || str_contains($name, 'table tennis')) {
            return '🏓';
        }
        if (str_contains($name, 'tug of war') || str_contains($name, 'rope')) {
            return '🪢';
        }
        if (str_contains($name, 'athletics') || str_contains($name, 'sprint') || str_contains($name, 'race')) {
            return '🏃';
        }

        return '🏅';
    }

    /**
     * Format display score for a team, including wickets and overs if Cricket.
     */
    public function formatTeamScore(int $teamNumber): string
    {
        if (! $this->result) {
            return '0';
        }

        $score = $teamNumber === 1 ? $this->result->team1_score : $this->result->team2_score;
        $wickets = $teamNumber === 1 ? $this->result->team1_wickets : $this->result->team2_wickets;
        $overs = $teamNumber === 1 ? $this->result->team1_overs : $this->result->team2_overs;

        $isCricket = str_contains(strtolower($this->sport?->name ?? ''), 'cricket');

        if ($isCricket && ($wickets !== null || !empty($overs))) {
            $formatted = (string) $score;
            if ($wickets !== null) {
                $formatted .= "/{$wickets}";
            }
            if (!empty($overs)) {
                $formatted .= " ({$overs} ov)";
            }
            return $formatted;
        }

        return (string) $score;
    }

    /**
     * Generate pre-formatted WhatsApp share message with real Unicode emojis and match details.
     */
    public function getWhatsAppShareTextAttribute(): string
    {
        $editionTitle = $this->edition?->title ?? 'KIU Sports Gala';
        $sportEmoji = $this->sport_emoji;
        $sportName = strtoupper($this->sport?->name ?? 'Match');
        $category = $this->sport?->category ? ' (' . strtoupper($this->sport->category) . ')' : '';
        $round = $this->round ? " • {$this->round}" : '';
        $t1 = $this->team1?->name ?? 'Team 1';
        $t2 = $this->team2?->name ?? 'Team 2';
        $matchUrl = route('matches.show', $this);

        $lines = [];
        $lines[] = "🏆 *{$editionTitle}*";
        $lines[] = "━━━━━━━━━━━━━━━━━━━━━";
        $lines[] = "{$sportEmoji} *{$sportName}{$category}*{$round}";
        $lines[] = "";

        if ($this->status === 'live') {
            $s1 = $this->formatTeamScore(1);
            $s2 = $this->formatTeamScore(2);
            $lines[] = "🔴 *LIVE MATCH UPDATE*";
            $lines[] = "👉 *{$t1}*: {$s1}";
            $lines[] = "👉 *{$t2}*: {$s2}";
            if ($this->result?->summary) {
                $lines[] = "📝 *Note:* {$this->result->summary}";
            }
            if ($this->venue?->name) {
                $lines[] = "📍 *Venue:* {$this->venue->name}";
            }
            $lines[] = "";
            $lines[] = "📲 *Live Scores & Commentary:*";
            $lines[] = $matchUrl;
        } elseif ($this->status === 'completed') {
            $s1 = $this->formatTeamScore(1);
            $s2 = $this->formatTeamScore(2);
            $lines[] = "✅ *FINAL RESULT*";
            $lines[] = "*{$t1}*: {$s1}";
            $lines[] = "*{$t2}*: {$s2}";
            if ($this->result?->winnerTeam) {
                $lines[] = "🥇 *Winner:* {$this->result->winnerTeam->name} 🏆";
            } elseif ($s1 == $s2) {
                $lines[] = "🤝 *Result:* Match Drawn";
            }
            if ($this->result?->playerOfTheMatch) {
                $lines[] = "⭐ *Player of the Match:* {$this->result->playerOfTheMatch->name}";
            }
            if ($this->result?->summary) {
                $lines[] = "📝 *Summary:* {$this->result->summary}";
            }
            $lines[] = "";
            $lines[] = "📊 *Full Scorecard & Action Photos:*";
            $lines[] = $matchUrl;
        } else {
            $lines[] = "📅 *UPCOMING FIXTURE*";
            $lines[] = "⚔️ *{$t1}* vs *{$t2}*";
            if ($this->venue?->name) {
                $lines[] = "📍 *Venue:* {$this->venue->name}";
            }
            if ($this->match_date) {
                $dateStr = $this->match_date->format('D, M d, Y');
                $timeStr = $this->match_time ? ' at ' . \Carbon\Carbon::parse($this->match_time)->format('g:i A') : '';
                $lines[] = "🕒 *Schedule:* {$dateStr}{$timeStr}";
            }
            $lines[] = "";
            $lines[] = "📲 *Match Center & Live Updates:*";
            $lines[] = $matchUrl;
        }

        $lines[] = "━━━━━━━━━━━━━━━━━━━━━";
        $lines[] = "_Directorate of Sports & Physical Education, KIU_";

        return implode("\n", $lines);
    }

    /**
     * Get the direct WhatsApp web/app share URL.
     */
    public function getWhatsAppShareUrlAttribute(): string
    {
        return 'https://api.whatsapp.com/send?text=' . urlencode($this->whatsapp_share_text);
    }
}

