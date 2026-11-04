<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Player;
use App\Models\Result;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsAppScoreShareTest extends TestCase
{
    use RefreshDatabase;

    private Edition $edition;
    private Sport $cricket;
    private Sport $football;
    private Team $team1;
    private Team $team2;
    private Venue $venue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-10',
            'status' => 'running',
        ]);

        $this->cricket = Sport::create([
            'name' => 'Cricket Men',
            'category' => 'boys',
            'scoring_type' => 'runs',
        ]);

        $this->football = Sport::create([
            'name' => 'Football Boys',
            'category' => 'boys',
            'scoring_type' => 'goals',
        ]);

        $dept1 = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $dept2 = Department::create(['name' => 'Management Sciences', 'code' => 'MS']);

        $this->team1 = Team::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricket->id,
            'department_id' => $dept1->id,
            'name' => 'CS Strikers',
        ]);

        $this->team2 = Team::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricket->id,
            'department_id' => $dept2->id,
            'name' => 'MS Kings',
        ]);

        $this->venue = Venue::create(['name' => 'Main Cricket Ground']);
    }

    public function test_fixture_model_generates_formatted_whatsapp_text_with_real_unicode_emojis_for_live_match(): void
    {
        $fixture = Fixture::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricket->id,
            'team1_id' => $this->team1->id,
            'team2_id' => $this->team2->id,
            'venue_id' => $this->venue->id,
            'round' => 'Final',
            'status' => 'live',
        ]);

        Result::create([
            'fixture_id' => $fixture->id,
            'team1_score' => 145,
            'team2_score' => 80,
            'summary' => '10 overs remaining',
        ]);

        $text = $fixture->whatsapp_share_text;

        // Check real Unicode emojis and structure
        $this->assertStringContainsString('🏆 *KIU Gala 2026*', $text);
        $this->assertStringContainsString('🏏', $text);
        $this->assertStringContainsString('*CRICKET MEN (BOYS)* • Final', $text);
        $this->assertStringContainsString('🔴 *LIVE MATCH UPDATE*', $text);
        $this->assertStringContainsString('CS Strikers*: 145', $text);
        $this->assertStringContainsString('MS Kings*: 80', $text);
        $this->assertStringContainsString('Main Cricket Ground', $text);
        $this->assertStringContainsString('10 overs remaining', $text);
        $this->assertStringContainsString(route('matches.show', $fixture), $text);

        // Check URL encoded format
        $url = $fixture->whatsapp_share_url;
        $this->assertStringStartsWith('https://api.whatsapp.com/send?text=', $url);
    }

    public function test_fixture_model_generates_completed_match_whatsapp_text_with_winner_and_potm(): void
    {
        $player = Player::create([
            'team_id' => $this->team1->id,
            'name' => 'Babar Khan',
            'reg_no' => 'KIU-CS-99',
        ]);

        $fixture = Fixture::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->football->id,
            'team1_id' => $this->team1->id,
            'team2_id' => $this->team2->id,
            'round' => 'Semi Final',
            'status' => 'completed',
        ]);

        Result::create([
            'fixture_id' => $fixture->id,
            'team1_score' => 3,
            'team2_score' => 1,
            'winner_team_id' => $this->team1->id,
            'player_of_the_match_id' => $player->id,
            'summary' => 'Spectacular hat-trick',
        ]);

        $text = $fixture->whatsapp_share_text;

        $this->assertStringContainsString('⚽', $text);
        $this->assertStringContainsString('✅ *FINAL RESULT*', $text);
        $this->assertStringContainsString('🥇 *Winner:* CS Strikers 🏆', $text);
        $this->assertStringContainsString('⭐ *Player of the Match:* Babar Khan', $text);
        $this->assertStringContainsString('Spectacular hat-trick', $text);
    }

    public function test_match_detail_page_renders_whatsapp_share_button(): void
    {
        $fixture = Fixture::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricket->id,
            'team1_id' => $this->team1->id,
            'team2_id' => $this->team2->id,
            'status' => 'live',
        ]);

        $response = $this->get(route('matches.show', $fixture));

        $response->assertStatus(200);
        $response->assertSee('whatsapp-share-btn');
        $response->assertSee('Share on WhatsApp');
        $response->assertSee('https://api.whatsapp.com/send?text=');
    }

    public function test_today_matches_and_quick_pad_render_whatsapp_share_links(): void
    {
        $fixture = Fixture::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricket->id,
            'team1_id' => $this->team1->id,
            'team2_id' => $this->team2->id,
            'match_date' => now()->toDateString(),
            'status' => 'live',
        ]);

        $todayResponse = $this->get(route('today'));
        $todayResponse->assertStatus(200);
        $todayResponse->assertSee('https://api.whatsapp.com/send?text=');

        $scorer = User::factory()->create(['role' => 'scorer']);
        $padResponse = $this->actingAs($scorer)->get(route('admin.score-entry.pad', $fixture));
        $padResponse->assertStatus(200);
        $padResponse->assertSee('https://api.whatsapp.com/send?text=');
    }

    public function test_live_api_returns_whatsapp_share_url(): void
    {
        $fixture = Fixture::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricket->id,
            'team1_id' => $this->team1->id,
            'team2_id' => $this->team2->id,
            'status' => 'live',
        ]);

        $response = $this->getJson(route('api.live-scores.show', $fixture));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $fixture->id,
        ]);
        $this->assertStringContainsString('https://api.whatsapp.com/send?text=', $response->json('whatsapp_share_url'));
    }

    public function test_cricket_match_whatsapp_and_score_formats_wickets_and_overs(): void
    {
        $fixture = Fixture::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricket->id,
            'team1_id' => $this->team1->id,
            'team2_id' => $this->team2->id,
            'venue_id' => $this->venue->id,
            'status' => 'live',
        ]);

        Result::create([
            'fixture_id' => $fixture->id,
            'team1_score' => 165,
            'team1_wickets' => 6,
            'team1_overs' => '20.0',
            'team2_score' => 78,
            'team2_wickets' => 3,
            'team2_overs' => '9.4',
        ]);

        $fixture->refresh();

        $this->assertEquals('165/6 (20.0 ov)', $fixture->formatTeamScore(1));
        $this->assertEquals('78/3 (9.4 ov)', $fixture->formatTeamScore(2));

        $text = $fixture->whatsapp_share_text;
        $this->assertStringContainsString('165/6 (20.0 ov)', $text);
        $this->assertStringContainsString('78/3 (9.4 ov)', $text);
    }
}
