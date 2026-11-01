<?php

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FixtureTest extends TestCase
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

    public function test_scorer_is_forbidden_from_fixtures(): void
    {
        $response = $this->actingAs($this->scorer)->get(route('admin.fixtures.index'));
        $response->assertForbidden();
    }

    public function test_admin_can_view_fixtures_index_and_filter(): void
    {
        $edition1 = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);
        $edition2 = Edition::create(['title' => 'Gala 2025', 'year' => 2025, 'status' => 'completed']);

        $sportCricket = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $sportFootball = Sport::create(['name' => 'Football', 'category' => 'boys']);

        $deptCS = Department::create(['name' => 'Computer Science']);
        $deptBBA = Department::create(['name' => 'Business Admin']);

        $team1 = Team::create(['edition_id' => $edition1->id, 'sport_id' => $sportCricket->id, 'department_id' => $deptCS->id, 'name' => 'CS Cricket']);
        $team2 = Team::create(['edition_id' => $edition1->id, 'sport_id' => $sportCricket->id, 'department_id' => $deptBBA->id, 'name' => 'BBA Cricket']);

        $team3 = Team::create(['edition_id' => $edition2->id, 'sport_id' => $sportFootball->id, 'department_id' => $deptCS->id, 'name' => 'CS Football 2025']);
        $team4 = Team::create(['edition_id' => $edition2->id, 'sport_id' => $sportFootball->id, 'department_id' => $deptBBA->id, 'name' => 'BBA Football 2025']);

        $fixture1 = Fixture::create([
            'edition_id' => $edition1->id,
            'sport_id' => $sportCricket->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'match_date' => '2026-11-05',
            'status' => 'scheduled',
            'round' => 'Group Stage',
        ]);

        $fixture2 = Fixture::create([
            'edition_id' => $edition2->id,
            'sport_id' => $sportFootball->id,
            'team1_id' => $team3->id,
            'team2_id' => $team4->id,
            'match_date' => '2025-10-10',
            'status' => 'completed',
            'round' => 'Final',
        ]);

        // Unfiltered index
        $res = $this->actingAs($this->admin)->get(route('admin.fixtures.index'));
        $res->assertOk();
        $res->assertSee('CS Cricket');
        $res->assertSee('CS Football 2025');

        // Filter by edition 1
        $resEdition = $this->actingAs($this->admin)->get(route('admin.fixtures.index', ['edition_id' => $edition1->id]));
        $resEdition->assertOk();
        $resEdition->assertSee('CS Cricket');
        $resEdition->assertDontSee('CS Football 2025');

        // Filter by sport
        $resSport = $this->actingAs($this->admin)->get(route('admin.fixtures.index', ['sport_id' => $sportFootball->id]));
        $resSport->assertOk();
        $resSport->assertSee('CS Football 2025');
        $resSport->assertDontSee('CS Cricket');

        // Filter by date
        $resDate = $this->actingAs($this->admin)->get(route('admin.fixtures.index', ['date' => '2026-11-05']));
        $resDate->assertOk();
        $resDate->assertSee('CS Cricket');
        $resDate->assertDontSee('CS Football 2025');
    }

    public function test_validation_fails_if_team1_and_team2_are_identical(): void
    {
        $edition = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);
        $sport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $dept = Department::create(['name' => 'Computer Science']);
        $team = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept->id, 'name' => 'CS Warriors']);

        $response = $this->actingAs($this->admin)->post(route('admin.fixtures.store'), [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team->id,
            'team2_id' => $team->id,
            'status' => 'scheduled',
        ]);

        $response->assertSessionHasErrors(['team1_id', 'team2_id']);
        $this->assertDatabaseCount('fixtures', 0);
    }

    public function test_validation_fails_if_teams_do_not_match_edition_or_sport(): void
    {
        $edition1 = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);
        $edition2 = Edition::create(['title' => 'Gala 2025', 'year' => 2025, 'status' => 'completed']);

        $sportCricket = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $sportFootball = Sport::create(['name' => 'Football', 'category' => 'boys']);

        $dept1 = Department::create(['name' => 'CS']);
        $dept2 = Department::create(['name' => 'BBA']);

        // Team 1 belongs to Edition 1 Cricket
        $team1 = Team::create(['edition_id' => $edition1->id, 'sport_id' => $sportCricket->id, 'department_id' => $dept1->id, 'name' => 'CS Cricket']);
        // Team 2 belongs to Edition 2 Football
        $team2 = Team::create(['edition_id' => $edition2->id, 'sport_id' => $sportFootball->id, 'department_id' => $dept2->id, 'name' => 'BBA Football']);

        $response = $this->actingAs($this->admin)->post(route('admin.fixtures.store'), [
            'edition_id' => $edition1->id,
            'sport_id' => $sportCricket->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'status' => 'scheduled',
        ]);

        $response->assertSessionHasErrors(['team2_id']);
        $this->assertDatabaseCount('fixtures', 0);
    }

    public function test_admin_can_create_update_and_delete_fixture(): void
    {
        $edition = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);
        $sport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'Computer Science']);
        $dept2 = Department::create(['name' => 'Mathematics']);
        $venue = Venue::create(['name' => 'Main Ground']);

        $team1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'CS XI']);
        $team2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'Math Tigers']);

        // Create
        $response = $this->actingAs($this->admin)->post(route('admin.fixtures.store'), [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'venue_id' => $venue->id,
            'match_date' => '2026-11-12',
            'match_time' => '14:30',
            'round' => 'Semi Final',
            'status' => 'scheduled',
        ]);

        $response->assertRedirect(route('admin.fixtures.index'));
        $this->assertDatabaseHas('fixtures', [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'venue_id' => $venue->id,
            'round' => 'Semi Final',
            'status' => 'scheduled',
        ]);

        $fixture = Fixture::first();

        // Edit page
        $editRes = $this->actingAs($this->admin)->get(route('admin.fixtures.edit', $fixture));
        $editRes->assertOk();
        $editRes->assertSee('Edit Fixture');

        // Update
        $updateRes = $this->actingAs($this->admin)->put(route('admin.fixtures.update', $fixture), [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'venue_id' => $venue->id,
            'match_date' => '2026-11-13',
            'match_time' => '16:00',
            'round' => 'Final',
            'status' => 'live',
        ]);

        $updateRes->assertRedirect(route('admin.fixtures.index'));
        $this->assertDatabaseHas('fixtures', [
            'id' => $fixture->id,
            'round' => 'Final',
            'status' => 'live',
        ]);

        // Delete
        $delRes = $this->actingAs($this->admin)->delete(route('admin.fixtures.destroy', $fixture));
        $delRes->assertRedirect(route('admin.fixtures.index'));
        $this->assertDatabaseMissing('fixtures', ['id' => $fixture->id]);
    }
}
