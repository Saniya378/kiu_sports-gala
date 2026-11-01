<?php

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use App\Models\Winner;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_and_scorer_is_forbidden_from_reports(): void
    {
        $guestResponse = $this->get(route('admin.reports.index'));
        $guestResponse->assertRedirect(route('login'));

        $scorer = User::factory()->create(['role' => 'scorer']);
        $scorerResponse = $this->actingAs($scorer)->get(route('admin.reports.index'));
        $scorerResponse->assertStatus(403);
    }

    public function test_admin_can_view_reports_hub(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response->assertStatus(200);
        $response->assertSee('Tournament Reports');
        $response->assertSee('Winners List by Edition');
        $response->assertSee('Fixtures List by Date');
        $response->assertSee('Teams List by Sport');
    }

    public function test_admin_can_generate_winners_report_with_print_button(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $edition = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);
        $dept1 = Department::create(['name' => 'Computer Science']);
        $dept2 = Department::create(['name' => 'Physics']);
        $sport = Sport::create(['name' => 'Volleyball', 'category' => 'boys']);

        Winner::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'winner_department_id' => $dept1->id,
            'runner_up_department_id' => $dept2->id,
            'final_score' => '3 - 1 sets',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.winners', ['edition_id' => $edition->id]));

        $response->assertStatus(200);
        $response->assertSee('Official Winners');
        $response->assertSee('window.print()', false); // Print button exists
        $response->assertSee('Computer Science');
        $response->assertSee('Physics');
        $response->assertSee('3 - 1 sets');
    }

    public function test_admin_can_generate_fixtures_report_by_date_with_print_button(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $edition = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);
        $dept1 = Department::create(['name' => 'Math']);
        $dept2 = Department::create(['name' => 'Bio']);
        $sport = Sport::create(['name' => 'Football', 'category' => 'boys']);
        $venue = Venue::create(['name' => 'Football Ground']);

        $t1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'Math FC']);
        $t2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'Bio FC']);

        $today = Carbon::today()->toDateString();
        Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $t1->id,
            'team2_id' => $t2->id,
            'venue_id' => $venue->id,
            'match_date' => $today,
            'match_time' => '11:00:00',
            'round' => 'Semi Final',
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.fixtures', ['date' => $today]));

        $response->assertStatus(200);
        $response->assertSee('Daily Match Fixtures');
        $response->assertSee('window.print()', false);
        $response->assertSee('Math FC');
        $response->assertSee('Bio FC');
        $response->assertSee('Football Ground');
    }

    public function test_admin_can_generate_teams_report_by_sport_with_print_button(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $edition = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);
        $dept = Department::create(['name' => 'Earth Sciences']);
        $sport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);

        $team = Team::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'department_id' => $dept->id,
            'name' => 'Earth Strikers',
            'status' => 'approved',
        ]);

        Player::create(['team_id' => $team->id, 'name' => 'Kashif Baig', 'reg_no' => 'KIU-ES-101']);

        $response = $this->actingAs($admin)->get(route('admin.reports.teams', [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Participating Teams');
        $response->assertSee('window.print()', false);
        $response->assertSee('Earth Strikers');
        $response->assertSee('Kashif Baig');
        $response->assertSee('KIU-ES-101');
    }
}
