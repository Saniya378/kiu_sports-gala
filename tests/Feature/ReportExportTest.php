<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $scorer;
    private Edition $edition;
    private Sport $sport;
    private Department $dept1;
    private Department $dept2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->scorer = User::factory()->create(['role' => 'scorer']);

        $this->edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-10',
            'status' => 'running',
        ]);

        $this->sport = Sport::create([
            'name' => 'Football Men',
            'category' => 'boys',
            'scoring_type' => 'goals',
        ]);

        $this->dept1 = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $this->dept2 = Department::create(['name' => 'Management Sciences', 'code' => 'MS']);
    }

    public function test_scorer_is_forbidden_from_report_exports(): void
    {
        $this->actingAs($this->scorer)->get(route('admin.reports.medals.export'))->assertStatus(403);
        $this->actingAs($this->scorer)->get(route('admin.reports.winners.export'))->assertStatus(403);
        $this->actingAs($this->scorer)->get(route('admin.reports.fixtures.export'))->assertStatus(403);
        $this->actingAs($this->scorer)->get(route('admin.reports.teams.export'))->assertStatus(403);
    }

    public function test_admin_can_export_medals_tally_csv(): void
    {
        Winner::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
            'winner_department_id' => $this->dept1->id,
            'runner_up_department_id' => $this->dept2->id,
            'final_score' => '2 - 1',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.medals.export', [
            'edition_id' => $this->edition->id,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=kiu-medal-tally-2026.csv');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Rank', $content);
        $this->assertStringContainsString('Department Name', $content);
        $this->assertStringContainsString('Gold Medals (5 pts)', $content);
        $this->assertStringContainsString('Computer Science', $content);
        $this->assertStringContainsString('Management Sciences', $content);
    }

    public function test_admin_can_export_winners_list_csv(): void
    {
        Winner::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
            'winner_department_id' => $this->dept1->id,
            'runner_up_department_id' => $this->dept2->id,
            'final_score' => '2 - 0 sets',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.winners.export', [
            'edition_id' => $this->edition->id,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=kiu-winners-2026.csv');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Sport Discipline', $content);
        $this->assertStringContainsString('Gold Medalist (Champion)', $content);
        $this->assertStringContainsString('Football Men', $content);
        $this->assertStringContainsString('Computer Science', $content);
        $this->assertStringContainsString('Management Sciences', $content);
    }

    public function test_admin_can_export_fixtures_schedule_csv(): void
    {
        $venue = Venue::create(['name' => 'Main Ground']);

        $team1 = Team::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
            'department_id' => $this->dept1->id,
            'name' => 'CS Strikers',
        ]);

        $team2 = Team::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
            'department_id' => $this->dept2->id,
            'name' => 'MS Kings',
        ]);

        $fixture = Fixture::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'venue_id' => $venue->id,
            'match_date' => '2026-11-05',
            'match_time' => '14:30',
            'round' => 'Final',
            'status' => 'live',
            'stream_url' => 'https://www.youtube.com/watch?v=sample',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.fixtures.export', [
            'edition_id' => $this->edition->id,
        ]));

        $response->assertStatus(200);

        $content = $response->streamedContent();
        $this->assertStringContainsString('Match ID', $content);
        $this->assertStringContainsString('CS Strikers', $content);
        $this->assertStringContainsString('MS Kings', $content);
        $this->assertStringContainsString('LIVE', $content);
        $this->assertStringContainsString('https://www.youtube.com/watch?v=sample', $content);
    }

    public function test_admin_can_export_teams_and_athletes_accreditation_csv(): void
    {
        $team = Team::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
            'department_id' => $this->dept1->id,
            'name' => 'CS Football Team',
        ]);

        $player = Player::create([
            'team_id' => $team->id,
            'name' => 'Ali Ahmed',
            'reg_no' => 'KIU-2026-CS-001',
            'phone' => '03451234567',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.reports.teams.export', [
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
        ]));

        $response->assertStatus(200);

        $content = $response->streamedContent();
        $this->assertStringContainsString('Edition', $content);
        $this->assertStringContainsString('Sport', $content);
        $this->assertStringContainsString('Ali Ahmed', $content);
        $this->assertStringContainsString('KIU-2026-CS-001', $content);
        $this->assertStringContainsString('/players/' . $player->id . '/card', $content);
    }
}
