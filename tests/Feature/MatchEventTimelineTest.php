<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\MatchEvent;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchEventTimelineTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $scorer;
    private Edition $edition;
    private Sport $sport;
    private Team $team1;
    private Team $team2;
    private Player $player1;
    private Fixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->scorer = User::factory()->create(['role' => 'scorer']);

        $this->edition = Edition::create(['title' => 'KIU Gala 2026', 'year' => 2026, 'status' => 'running']);
        $this->sport = Sport::create(['name' => 'Football', 'category' => 'boys']);

        $dept1 = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $dept2 = Department::create(['name' => 'Business Admin', 'code' => 'BBA']);

        $this->team1 = Team::create(['edition_id' => $this->edition->id, 'sport_id' => $this->sport->id, 'department_id' => $dept1->id, 'name' => 'CS Strikers']);
        $this->team2 = Team::create(['edition_id' => $this->edition->id, 'sport_id' => $this->sport->id, 'department_id' => $dept2->id, 'name' => 'BBA Hawks']);

        $this->player1 = Player::create([
            'team_id' => $this->team1->id,
            'name' => 'Ali Ahmed',
            'reg_no' => '2023-CS-042',
        ]);

        $this->fixture = Fixture::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
            'team1_id' => $this->team1->id,
            'team2_id' => $this->team2->id,
            'match_date' => '2026-11-05',
            'status' => 'live',
        ]);
    }

    public function test_guest_cannot_record_match_events(): void
    {
        $response = $this->post(route('admin.fixtures.events.store', $this->fixture), [
            'event_type' => 'goal',
            'title' => 'Goal',
        ]);

        $response->assertRedirect(route('login'));
    }

    public function test_scorer_can_create_match_event_via_ajax(): void
    {
        $response = $this->actingAs($this->scorer)
            ->withHeaders([
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->post(route('admin.fixtures.events.store', $this->fixture), [
                'team_id' => $this->team1->id,
                'player_id' => $this->player1->id,
                'minute' => "14'",
                'event_type' => 'goal',
                'title' => 'Goal Scored!',
                'description' => 'Header into top corner from a corner kick',
            ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'event' => [
                    'minute' => "14'",
                    'icon' => '⚽',
                    'title' => 'Goal Scored!',
                    'player' => 'Ali Ahmed',
                ],
            ]);

        $this->assertDatabaseHas('match_events', [
            'fixture_id' => $this->fixture->id,
            'team_id' => $this->team1->id,
            'player_id' => $this->player1->id,
            'minute' => "14'",
            'event_type' => 'goal',
            'title' => 'Goal Scored!',
        ]);
    }

    public function test_public_match_detail_page_displays_timeline_events(): void
    {
        MatchEvent::create([
            'fixture_id' => $this->fixture->id,
            'team_id' => $this->team1->id,
            'player_id' => $this->player1->id,
            'minute' => "23'",
            'event_type' => 'goal',
            'title' => 'Spectacular Volley Goal',
            'description' => 'Ali strikes from outside the penalty box',
        ]);

        MatchEvent::create([
            'fixture_id' => $this->fixture->id,
            'team_id' => $this->team2->id,
            'minute' => "38'",
            'event_type' => 'yellow_card',
            'title' => 'Yellow Card',
            'description' => 'Tactical foul near midfield',
        ]);

        $response = $this->get(route('matches.show', $this->fixture));

        $response->assertOk();
        $response->assertSee('Match Timeline &amp; Key Moments', false);
        $response->assertSee('Spectacular Volley Goal');
        $response->assertSee('Yellow Card');
        $response->assertSee("23'");
        $response->assertSee("38'");
        $response->assertSee('Ali strikes from outside the penalty box');
    }

    public function test_live_api_returns_timeline_events(): void
    {
        MatchEvent::create([
            'fixture_id' => $this->fixture->id,
            'team_id' => $this->team1->id,
            'minute' => "10'",
            'event_type' => 'goal',
            'title' => 'Early Opening Goal',
        ]);

        $response = $this->getJson(route('api.live-scores.show', $this->fixture));

        $response->assertOk()
            ->assertJsonPath('events.0.title', 'Early Opening Goal')
            ->assertJsonPath('events.0.icon', '⚽')
            ->assertJsonPath('events.0.minute', "10'");
    }

    public function test_scorer_can_delete_match_event(): void
    {
        $event = MatchEvent::create([
            'fixture_id' => $this->fixture->id,
            'event_type' => 'note',
            'title' => 'Half Time Break',
        ]);

        $response = $this->actingAs($this->scorer)
            ->delete(route('admin.fixtures.events.destroy', [$this->fixture, $event]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('match_events', ['id' => $event->id]);
    }
}

