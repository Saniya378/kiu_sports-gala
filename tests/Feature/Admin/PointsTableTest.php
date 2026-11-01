<?php

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use App\Services\PointsTable;
use App\Services\PointsTableService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointsTableTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $scorer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@kiu.test',
            'role' => 'admin',
        ]);

        $this->scorer = User::factory()->create([
            'email' => 'scorer@kiu.test',
            'role' => 'scorer',
        ]);
    }

    public function test_guest_is_redirected_and_scorer_is_forbidden(): void
    {
        $this->get(route('admin.points-table.index'))->assertRedirect(route('login'));
        $this->actingAs($this->scorer)->get(route('admin.points-table.index'))->assertForbidden();
    }

    public function test_points_table_calculates_played_won_drawn_lost_and_points_correctly(): void
    {
        $edition = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);
        $sport = Sport::create(['name' => 'Football', 'category' => 'boys']);
        $deptA = Department::create(['name' => 'Computer Science']);
        $deptB = Department::create(['name' => 'Business Admin']);
        $deptC = Department::create(['name' => 'Mathematics']);

        $teamA = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $deptA->id, 'name' => 'Alpha Warriors']);
        $teamB = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $deptB->id, 'name' => 'Beta Knights']);
        $teamC = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $deptC->id, 'name' => 'Gamma Champions']);

        // Match 1: Alpha vs Beta -> Alpha wins 3 - 1 (Alpha +3 pts, Beta +0 pts)
        $fix1 = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $teamA->id,
            'team2_id' => $teamB->id,
            'status' => 'completed',
        ]);
        Result::create([
            'fixture_id' => $fix1->id,
            'team1_score' => 3,
            'team2_score' => 1,
            'winner_team_id' => $teamA->id,
        ]);

        // Match 2: Beta vs Gamma -> Draw 2 - 2 (Beta +1 pt, Gamma +1 pt)
        $fix2 = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $teamB->id,
            'team2_id' => $teamC->id,
            'status' => 'completed',
        ]);
        Result::create([
            'fixture_id' => $fix2->id,
            'team1_score' => 2,
            'team2_score' => 2,
            'winner_team_id' => null, // draw
        ]);

        // Match 3: Alpha vs Gamma -> Scheduled, no result yet (should NOT count)
        Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $teamA->id,
            'team2_id' => $teamC->id,
            'status' => 'scheduled',
        ]);

        $service = new PointsTableService();
        $standings = $service->calculate($edition, $sport);

        // Assert 3 teams returned
        $this->assertCount(3, $standings);

        // Standings sorted by points desc:
        // Rank 1: Alpha Warriors (P:1, W:1, D:0, L:0, PTS:3)
        $this->assertEquals($teamA->id, $standings[0]['team']->id);
        $this->assertEquals(1, $standings[0]['played']);
        $this->assertEquals(1, $standings[0]['won']);
        $this->assertEquals(0, $standings[0]['drawn']);
        $this->assertEquals(0, $standings[0]['lost']);
        $this->assertEquals(3, $standings[0]['points']);

        // Rank 2: Gamma Champions (P:1, W:0, D:1, L:0, PTS:1, GD:0)
        $this->assertEquals($teamC->id, $standings[1]['team']->id);
        $this->assertEquals(1, $standings[1]['played']);
        $this->assertEquals(0, $standings[1]['won']);
        $this->assertEquals(1, $standings[1]['drawn']);
        $this->assertEquals(0, $standings[1]['lost']);
        $this->assertEquals(1, $standings[1]['points']);

        // Rank 3: Beta Knights (P:2, W:0, D:1, L:1, PTS:1, GD:-2)
        $this->assertEquals($teamB->id, $standings[2]['team']->id);
        $this->assertEquals(2, $standings[2]['played']);
        $this->assertEquals(0, $standings[2]['won']);
        $this->assertEquals(1, $standings[2]['drawn']);
        $this->assertEquals(1, $standings[2]['lost']);
        $this->assertEquals(1, $standings[2]['points']);

        // Test static PointsTable class alias
        $staticStandings = PointsTable::for($edition->id, $sport->id);
        $this->assertEquals($standings->pluck('points')->all(), $staticStandings->pluck('points')->all());

        // Admin page verification
        $response = $this->actingAs($this->admin)->get(route('admin.points-table.index', [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
        ]));

        $response->assertOk();
        $response->assertSee('Points Table');
        $response->assertSee('Alpha Warriors');
        $response->assertSee('Beta Knights');
        $response->assertSee('Gamma Champions');
    }
}
