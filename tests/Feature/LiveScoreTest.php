<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveScoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_live_scores_returns_todays_matches_and_scores(): void
    {
        $edition = Edition::create(['year' => 2026, 'title' => 'Gala 2026', 'status' => 'running']);
        $sport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'CS', 'code' => 'CS']);
        $dept2 = Department::create(['name' => 'BBA', 'code' => 'BBA']);
        $team1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'CS Thunder']);
        $team2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'BBA Hawks']);
        $venue = Venue::create(['name' => 'Main Ground']);

        $fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'venue_id' => $venue->id,
            'match_date' => Carbon::today(),
            'match_time' => '10:00:00',
            'status' => 'live',
            'round' => 'Group Match',
        ]);

        Result::create([
            'fixture_id' => $fixture->id,
            'team1_score' => 142,
            'team2_score' => 98,
            'summary' => 'CS dominating with quick wickets',
        ]);

        $response = $this->getJson(route('api.live-scores.index'));

        $response->assertOk()
            ->assertJson([
                'live_count' => 1,
            ])
            ->assertJsonPath('matches.0.id', $fixture->id)
            ->assertJsonPath('matches.0.team1_score', 142)
            ->assertJsonPath('matches.0.team2_score', 98)
            ->assertJsonPath('matches.0.status', 'live');
    }

    public function test_api_single_match_live_endpoint_returns_realtime_data(): void
    {
        $edition = Edition::create(['year' => 2026, 'title' => 'Gala 2026', 'status' => 'running']);
        $sport = Sport::create(['name' => 'Football', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'Math', 'code' => 'MTH']);
        $dept2 = Department::create(['name' => 'Physics', 'code' => 'PHY']);
        $team1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'Math FC']);
        $team2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'Phy FC']);

        $fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'match_date' => Carbon::today(),
            'status' => 'live',
        ]);

        Result::create([
            'fixture_id' => $fixture->id,
            'team1_score' => 2,
            'team2_score' => 1,
            'winner_team_id' => null,
            'summary' => 'Second half underway',
        ]);

        $response = $this->getJson(route('api.live-scores.show', $fixture));

        $response->assertOk()
            ->assertJsonPath('id', $fixture->id)
            ->assertJsonPath('status', 'live')
            ->assertJsonPath('team1.name', 'Math FC')
            ->assertJsonPath('team1.score', 2)
            ->assertJsonPath('team2.score', 1)
            ->assertJsonPath('result.summary', 'Second half underway');
    }

    public function test_scorer_update_reflects_in_live_api(): void
    {
        $scorer = User::factory()->create(['role' => 'scorer']);
        $edition = Edition::create(['year' => 2026, 'title' => 'Gala 2026', 'status' => 'running']);
        $sport = Sport::create(['name' => 'Volleyball', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'Dept 1']);
        $dept2 = Department::create(['name' => 'Dept 2']);
        $team1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'Team A']);
        $team2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'Team B']);

        $fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'status' => 'scheduled',
        ]);

        // Scorer sets it to live with initial score
        $this->actingAs($scorer)->post(route('admin.score-entry.store'), [
            'fixture_id' => $fixture->id,
            'status' => 'live',
            'team1_score' => 15,
            'team2_score' => 12,
            'summary' => 'First set in progress',
        ]);

        // Check live API
        $response = $this->getJson(route('api.live-scores.show', $fixture));
        $response->assertOk()
            ->assertJsonPath('status', 'live')
            ->assertJsonPath('team1.score', 15)
            ->assertJsonPath('team2.score', 12);

        // Scorer completes match and sets winner
        $this->actingAs($scorer)->post(route('admin.score-entry.store'), [
            'fixture_id' => $fixture->id,
            'status' => 'completed',
            'team1_score' => 25,
            'team2_score' => 18,
            'summary' => 'Team A takes set and match',
        ]);

        $completedResponse = $this->getJson(route('api.live-scores.show', $fixture));
        $completedResponse->assertOk()
            ->assertJsonPath('status', 'completed')
            ->assertJsonPath('team1.score', 25)
            ->assertJsonPath('team1.is_winner', true);
    }

    public function test_match_detail_and_today_views_contain_live_sync_scripts(): void
    {
        $edition = Edition::create(['year' => 2026, 'title' => 'Gala 2026', 'status' => 'running']);
        $sport = Sport::create(['name' => 'Volleyball', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'CS']);
        $dept2 = Department::create(['name' => 'Bio']);
        $team1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'Team 1']);
        $team2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'Team 2']);

        $fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'match_date' => Carbon::today(),
            'status' => 'live',
        ]);

        // Test Today page
        $todayResponse = $this->get(route('today'));
        $todayResponse->assertOk()
            ->assertSee('Live Auto-Sync')
            ->assertSee('data-fixture-card="' . $fixture->id . '"', false);

        // Test Match Detail page
        $detailResponse = $this->get(route('matches.show', $fixture));
        $detailResponse->assertOk()
            ->assertSee('live-sync-indicator')
            ->assertSee(route('api.live-scores.show', $fixture));
    }

    public function test_sse_stream_all_endpoint_returns_event_stream_headers_and_payload(): void
    {
        $edition = Edition::create(['year' => 2026, 'title' => 'Gala 2026', 'status' => 'running']);
        $sport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'CS']);
        $dept2 = Department::create(['name' => 'BBA']);
        $team1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'CS XI']);
        $team2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'BBA XI']);

        $fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'match_date' => Carbon::today(),
            'status' => 'live',
        ]);

        $response = $this->get(route('api.live-scores.stream'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/event-stream; charset=UTF-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('event: today-live-scores', $content);
        $this->assertStringContainsString((string) $fixture->id, $content);
    }

    public function test_sse_stream_single_match_endpoint_returns_event_stream(): void
    {
        $edition = Edition::create(['year' => 2026, 'title' => 'Gala 2026', 'status' => 'running']);
        $sport = Sport::create(['name' => 'Football', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'CS']);
        $dept2 = Department::create(['name' => 'Bio']);
        $team1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'CS FC']);
        $team2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'Bio FC']);

        $fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'status' => 'live',
        ]);

        Result::create([
            'fixture_id' => $fixture->id,
            'team1_score' => 3,
            'team2_score' => 2,
        ]);

        $response = $this->get(route('api.live-scores.stream.match', $fixture));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/event-stream; charset=UTF-8');
        $content = $response->streamedContent();
        $this->assertStringContainsString('event: match-live-score', $content);
        $this->assertStringContainsString('CS FC', $content);
    }
}

