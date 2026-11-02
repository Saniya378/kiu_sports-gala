<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Sport;
use App\Models\User;
use App\Models\Winner;
use App\Services\MedalTallyService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedalTallyTest extends TestCase
{
    use RefreshDatabase;

    public function test_medal_tally_service_calculates_standings_and_points_correctly(): void
    {
        $edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'status' => 'running',
        ]);

        $sport1 = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $sport2 = Sport::create(['name' => 'Football', 'category' => 'boys']);

        $deptCs = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $deptBba = Department::create(['name' => 'Business Administration', 'code' => 'BBA']);
        $deptMath = Department::create(['name' => 'Mathematics', 'code' => 'MTH']);

        // Winner 1: Cricket -> Gold CS, Silver BBA, Bronze Math
        Winner::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport1->id,
            'winner_department_id' => $deptCs->id,
            'runner_up_department_id' => $deptBba->id,
            'second_runner_up_department_id' => $deptMath->id,
            'final_score' => '150/4 vs 120/8',
        ]);

        // Winner 2: Football -> Gold Math, Silver CS, Bronze BBA
        Winner::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport2->id,
            'winner_department_id' => $deptMath->id,
            'runner_up_department_id' => $deptCs->id,
            'second_runner_up_department_id' => $deptBba->id,
            'final_score' => '2 - 0',
        ]);

        $service = new MedalTallyService();
        $tallies = $service->calculateForEdition($edition);

        $this->assertCount(3, $tallies);

        // CS: 1 Gold (5), 1 Silver (3) = 8 Points
        // Math: 1 Gold (5), 1 Bronze (1) = 6 Points
        // BBA: 1 Silver (3), 1 Bronze (1) = 4 Points
        $first = $tallies->first();
        $this->assertEquals($deptCs->id, $first['department']->id);
        $this->assertEquals(1, $first['rank']);
        $this->assertEquals(1, $first['gold']);
        $this->assertEquals(1, $first['silver']);
        $this->assertEquals(0, $first['bronze']);
        $this->assertEquals(8, $first['points']);

        $second = $tallies->get(1);
        $this->assertEquals($deptMath->id, $second['department']->id);
        $this->assertEquals(2, $second['rank']);
        $this->assertEquals(1, $second['gold']);
        $this->assertEquals(0, $second['silver']);
        $this->assertEquals(1, $second['bronze']);
        $this->assertEquals(6, $second['points']);

        $third = $tallies->get(2);
        $this->assertEquals($deptBba->id, $third['department']->id);
        $this->assertEquals(3, $third['rank']);
        $this->assertEquals(4, $third['points']);
    }

    public function test_admin_can_save_winner_with_bronze_department(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $edition = Edition::create(['year' => 2026, 'title' => 'Gala 2026', 'status' => 'running']);
        $sport = Sport::create(['name' => 'Volleyball', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'Physics', 'code' => 'PHY']);
        $dept2 = Department::create(['name' => 'Chemistry', 'code' => 'CHM']);
        $dept3 = Department::create(['name' => 'Bio', 'code' => 'BIO']);

        $response = $this->actingAs($admin)->post(route('admin.winners.store'), [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'winner_department_id' => $dept1->id,
            'runner_up_department_id' => $dept2->id,
            'second_runner_up_department_id' => $dept3->id,
            'final_score' => '3 - 1 sets',
        ]);

        $response->assertRedirect(route('admin.winners.index'));

        $this->assertDatabaseHas('winners', [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'winner_department_id' => $dept1->id,
            'runner_up_department_id' => $dept2->id,
            'second_runner_up_department_id' => $dept3->id,
            'final_score' => '3 - 1 sets',
        ]);
    }

    public function test_public_results_page_displays_overall_medal_tally(): void
    {
        $edition = Edition::create(['year' => 2026, 'title' => 'KIU Gala 2026', 'status' => 'running']);
        $sport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $dept = Department::create(['name' => 'Computer Science', 'code' => 'CS']);

        Winner::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'winner_department_id' => $dept->id,
            'runner_up_department_id' => null,
            'second_runner_up_department_id' => null,
            'final_score' => '100 vs 80',
        ]);

        $response = $this->get(route('results', ['edition_id' => $edition->id, 'sport_id' => $sport->id]));

        $response->assertOk();
        $response->assertSee('Overall Department Medal Tally');
        $response->assertSee('Computer Science');
        $response->assertSee('KIU General Trophy Standings');
    }

    public function test_admin_can_generate_medals_report_and_scorer_is_forbidden(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $scorer = User::factory()->create(['role' => 'scorer']);
        $edition = Edition::create(['year' => 2026, 'title' => 'KIU Gala 2026', 'status' => 'running']);

        // Scorer forbidden
        $this->actingAs($scorer)
            ->get(route('admin.reports.medals', ['edition_id' => $edition->id]))
            ->assertForbidden();

        // Admin allowed
        $this->actingAs($admin)
            ->get(route('admin.reports.medals', ['edition_id' => $edition->id]))
            ->assertOk()
            ->assertSee('Official General Trophy & Department Medal Tally', false);
    }
}
