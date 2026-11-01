<?php

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ScoreEntryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $scorer;
    private Fixture $fixture;
    private Team $team1;
    private Team $team2;

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

        $edition = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);
        $sport = Sport::create(['name' => 'Football', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'Computer Science']);
        $dept2 = Department::create(['name' => 'Business Admin']);

        $this->team1 = Team::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'department_id' => $dept1->id,
            'name' => 'CS Strikers',
        ]);

        $this->team2 = Team::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'department_id' => $dept2->id,
            'name' => 'BBA Tigers',
        ]);

        $this->fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $this->team1->id,
            'team2_id' => $this->team2->id,
            'status' => 'scheduled',
            'match_date' => '2026-11-10',
        ]);
    }

    public function test_guest_is_redirected_from_score_entry(): void
    {
        $response = $this->get(route('admin.score-entry.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_scorer_can_access_score_entry(): void
    {
        $response = $this->actingAs($this->scorer)->get(route('admin.score-entry.index'));
        $response->assertOk();
        $response->assertSee('Score Entry');
        $response->assertSee('CS Strikers');
    }

    public function test_scorer_is_forbidden_from_other_admin_pages(): void
    {
        $this->actingAs($this->scorer)->get(route('admin.editions.index'))->assertForbidden();
        $this->actingAs($this->scorer)->get(route('admin.departments.index'))->assertForbidden();
        $this->actingAs($this->scorer)->get(route('admin.venues.index'))->assertForbidden();
        $this->actingAs($this->scorer)->get(route('admin.sports.index'))->assertForbidden();
        $this->actingAs($this->scorer)->get(route('admin.teams.index'))->assertForbidden();
        $this->actingAs($this->scorer)->get(route('admin.fixtures.index'))->assertForbidden();
    }

    public function test_admin_can_access_score_entry_and_admin_pages(): void
    {
        $this->actingAs($this->admin)->get(route('admin.score-entry.index'))->assertOk();
        $this->actingAs($this->admin)->get(route('admin.fixtures.index'))->assertOk();
    }

    public function test_save_result_auto_detects_team1_as_winner_and_marks_completed(): void
    {
        $response = $this->actingAs($this->scorer)->post(route('admin.score-entry.store'), [
            'fixture_id' => $this->fixture->id,
            'team1_score' => 3,
            'team2_score' => 1,
            'summary' => 'Great performance by CS Strikers.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('results', [
            'fixture_id' => $this->fixture->id,
            'team1_score' => 3,
            'team2_score' => 1,
            'winner_team_id' => $this->team1->id,
            'summary' => 'Great performance by CS Strikers.',
        ]);

        $this->fixture->refresh();
        $this->assertEquals('completed', $this->fixture->status);
    }

    public function test_save_result_auto_detects_team2_as_winner_and_marks_completed(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.score-entry.store'), [
            'fixture_id' => $this->fixture->id,
            'team1_score' => 0,
            'team2_score' => 2,
            'summary' => 'Clean sheet for BBA.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('results', [
            'fixture_id' => $this->fixture->id,
            'team1_score' => 0,
            'team2_score' => 2,
            'winner_team_id' => $this->team2->id,
        ]);

        $this->fixture->refresh();
        $this->assertEquals('completed', $this->fixture->status);
    }

    public function test_save_result_draw_sets_winner_to_null_and_marks_completed(): void
    {
        $response = $this->actingAs($this->scorer)->post(route('admin.score-entry.store'), [
            'fixture_id' => $this->fixture->id,
            'team1_score' => 2,
            'team2_score' => 2,
            'summary' => 'Exciting draw.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('results', [
            'fixture_id' => $this->fixture->id,
            'team1_score' => 2,
            'team2_score' => 2,
            'winner_team_id' => null,
        ]);

        $this->fixture->refresh();
        $this->assertEquals('completed', $this->fixture->status);
    }

    public function test_updating_existing_result_preserves_single_row_and_updates_winner(): void
    {
        Result::create([
            'fixture_id' => $this->fixture->id,
            'team1_score' => 1,
            'team2_score' => 1,
            'winner_team_id' => null,
        ]);

        $this->assertDatabaseCount('results', 1);

        // Update to Team 1 winning
        $response = $this->actingAs($this->scorer)->post(route('admin.score-entry.store'), [
            'fixture_id' => $this->fixture->id,
            'team1_score' => 4,
            'team2_score' => 2,
            'summary' => 'Corrected score.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('results', 1);
        $this->assertDatabaseHas('results', [
            'fixture_id' => $this->fixture->id,
            'team1_score' => 4,
            'team2_score' => 2,
            'winner_team_id' => $this->team1->id,
        ]);
    }
}

