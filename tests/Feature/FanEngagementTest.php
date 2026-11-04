<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FanEngagementTest extends TestCase
{
    use RefreshDatabase;

    private Fixture $fixture;
    private Team $team1;
    private Team $team2;

    protected function setUp(): void
    {
        parent::setUp();

        $edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'status' => 'running',
            'start_date' => now()->subDay(),
            'end_date' => now()->addDays(4),
        ]);

        $sport = Sport::create([
            'name' => 'Cricket',
            'type' => 'team',
            'category' => 'boys',
        ]);

        $dept1 = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $dept2 = Department::create(['name' => 'Business Management', 'code' => 'BM']);

        $this->team1 = Team::create([
            'edition_id' => $edition->id,
            'department_id' => $dept1->id,
            'sport_id' => $sport->id,
            'name' => 'CS Strikers',
            'status' => 'approved',
        ]);

        $this->team2 = Team::create([
            'edition_id' => $edition->id,
            'department_id' => $dept2->id,
            'sport_id' => $sport->id,
            'name' => 'BM Titans',
            'status' => 'approved',
        ]);

        $this->fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $this->team1->id,
            'team2_id' => $this->team2->id,
            'match_date' => now()->toDateString(),
            'match_time' => '10:00:00',
            'status' => 'live',
            'round' => 'Final',
        ]);
    }

    public function test_match_detail_displays_fan_zone_poll_and_cheer_buttons(): void
    {
        $response = $this->get(route('matches.show', $this->fixture));

        $response->assertStatus(200);
        $response->assertSee('Live Department Fan Zone');
        $response->assertSee('Who will win this match?');
        $response->assertSee('CS Strikers');
        $response->assertSee('BM Titans');
        $response->assertSee('Send Live Cheers to Ground');
        $response->assertSee('reaction-count-fire');
        $response->assertSee('reaction-count-clap');
    }

    public function test_fan_can_cast_vote_and_stats_are_calculated(): void
    {
        $response = $this->postJson(route('matches.vote', $this->fixture), [
            'team_id' => $this->team1->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'user_voted_team_id' => $this->team1->id,
            'stats' => [
                'total' => 1,
                'team1_votes' => 1,
                'team2_votes' => 0,
                'team1_percentage' => 100,
                'team2_percentage' => 0,
            ],
        ]);

        $this->assertDatabaseHas('match_fan_votes', [
            'fixture_id' => $this->fixture->id,
            'team_id' => $this->team1->id,
        ]);
    }

    public function test_fan_vote_rejects_unrelated_team(): void
    {
        $unrelatedDept = Department::create(['name' => 'Chemistry', 'code' => 'CHEM']);
        $otherTeam = Team::create([
            'edition_id' => $this->fixture->edition_id,
            'department_id' => $unrelatedDept->id,
            'sport_id' => $this->fixture->sport_id,
            'name' => 'Chem Warriors',
            'status' => 'approved',
        ]);

        $response = $this->postJson(route('matches.vote', $this->fixture), [
            'team_id' => $otherTeam->id,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['team_id']);
    }

    public function test_fan_can_send_live_emoji_reactions(): void
    {
        // 1st fire reaction
        $r1 = $this->postJson(route('matches.react', $this->fixture), [
            'reaction' => 'fire',
        ]);

        $r1->assertStatus(200);
        $r1->assertJson([
            'success' => true,
            'reaction' => 'fire',
            'count' => 1,
        ]);

        // 2nd fire reaction
        $r2 = $this->postJson(route('matches.react', $this->fixture), [
            'reaction' => 'fire',
        ]);
        $r2->assertStatus(200);
        $r2->assertJson([
            'success' => true,
            'reaction' => 'fire',
            'count' => 2,
        ]);

        // Clap reaction
        $r3 = $this->postJson(route('matches.react', $this->fixture), [
            'reaction' => 'clap',
        ]);
        $r3->assertStatus(200);
        $r3->assertJson([
            'success' => true,
            'reaction' => 'clap',
            'count' => 1,
        ]);

        $this->assertDatabaseHas('match_reactions', [
            'fixture_id' => $this->fixture->id,
            'reaction' => 'fire',
            'count' => 2,
        ]);
    }

    public function test_reaction_validation_rejects_unsupported_emoji(): void
    {
        $response = $this->postJson(route('matches.react', $this->fixture), [
            'reaction' => 'unsupported_reaction',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['reaction']);
    }

    public function test_fan_stats_endpoint_returns_json_structure(): void
    {
        $response = $this->getJson(route('matches.fan-stats', $this->fixture));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'poll' => [
                'total',
                'team1_votes',
                'team2_votes',
                'team1_percentage',
                'team2_percentage',
            ],
            'reactions' => [
                'fire',
                'clap',
                'cheer',
                'trophy',
            ],
        ]);
    }
}

