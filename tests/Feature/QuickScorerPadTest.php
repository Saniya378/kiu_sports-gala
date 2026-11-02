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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuickScorerPadTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $scorer;
    private Edition $edition;
    private Sport $cricketSport;
    private Sport $footballSport;
    private Fixture $cricketFixture;
    private Fixture $footballFixture;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->scorer = User::factory()->create(['role' => 'scorer']);

        $this->edition = Edition::create(['title' => 'KIU Gala 2026', 'year' => 2026, 'status' => 'running']);

        $this->cricketSport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $this->footballSport = Sport::create(['name' => 'Football', 'category' => 'boys']);

        $dept1 = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $dept2 = Department::create(['name' => 'Business Admin', 'code' => 'BBA']);

        $cTeam1 = Team::create(['edition_id' => $this->edition->id, 'sport_id' => $this->cricketSport->id, 'department_id' => $dept1->id, 'name' => 'CS Strikers']);
        $cTeam2 = Team::create(['edition_id' => $this->edition->id, 'sport_id' => $this->cricketSport->id, 'department_id' => $dept2->id, 'name' => 'BBA Kings']);

        $fTeam1 = Team::create(['edition_id' => $this->edition->id, 'sport_id' => $this->footballSport->id, 'department_id' => $dept1->id, 'name' => 'CS FC']);
        $fTeam2 = Team::create(['edition_id' => $this->edition->id, 'sport_id' => $this->footballSport->id, 'department_id' => $dept2->id, 'name' => 'BBA FC']);

        $this->cricketFixture = Fixture::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricketSport->id,
            'team1_id' => $cTeam1->id,
            'team2_id' => $cTeam2->id,
            'match_date' => '2026-11-05',
            'status' => 'scheduled',
        ]);

        $this->footballFixture = Fixture::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->footballSport->id,
            'team1_id' => $fTeam1->id,
            'team2_id' => $fTeam2->id,
            'match_date' => '2026-11-05',
            'status' => 'live',
        ]);
    }

    public function test_guest_is_redirected_from_quick_scorer_pad(): void
    {
        $response = $this->get(route('admin.score-entry.pad', $this->cricketFixture));
        $response->assertRedirect(route('login'));
    }

    public function test_scorer_and_admin_can_access_quick_scorer_pad(): void
    {
        $response = $this->actingAs($this->scorer)->get(route('admin.score-entry.pad', $this->cricketFixture));
        $response->assertStatus(200);
        $response->assertSee('CS Strikers');
        $response->assertSee('BBA Kings');
        $response->assertSee('Broadcast Live Score');

        $adminResponse = $this->actingAs($this->admin)->get(route('admin.score-entry.pad', $this->cricketFixture));
        $adminResponse->assertStatus(200);
    }

    public function test_quick_pad_displays_cricket_sport_controls(): void
    {
        $response = $this->actingAs($this->scorer)->get(route('admin.score-entry.pad', $this->cricketFixture));

        $response->assertStatus(200);
        $response->assertSee('+4 🏏');
        $response->assertSee('+6 🚀');
        $response->assertSee('Runs');
    }

    public function test_quick_pad_displays_football_sport_controls(): void
    {
        $response = $this->actingAs($this->scorer)->get(route('admin.score-entry.pad', $this->footballFixture));

        $response->assertStatus(200);
        $response->assertSee('+1 GOAL ⚽');
        $response->assertSee('Goals');
        $response->assertSee('LIVE MATCH');
    }

    public function test_ajax_score_submission_updates_score_and_returns_json(): void
    {
        $response = $this->actingAs($this->scorer)
            ->withHeaders([
                'Accept' => 'application/json',
                'X-Requested-With' => 'XMLHttpRequest',
            ])
            ->post(route('admin.score-entry.store'), [
                'fixture_id' => $this->footballFixture->id,
                'team1_score' => 3,
                'team2_score' => 1,
                'status' => 'live',
                'summary' => '78th minute - CS leading',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'fixture' => [
                'id' => $this->footballFixture->id,
                'status' => 'live',
                'team1_score' => 3,
                'team2_score' => 1,
            ],
        ]);

        $this->assertDatabaseHas('results', [
            'fixture_id' => $this->footballFixture->id,
            'team1_score' => '3',
            'team2_score' => '1',
            'summary' => '78th minute - CS leading',
        ]);

        $this->assertEquals('live', $this->footballFixture->fresh()->status);
    }
}
