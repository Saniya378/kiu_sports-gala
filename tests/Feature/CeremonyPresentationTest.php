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
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CeremonyPresentationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ceremony_screen_renders_successfully(): void
    {
        $edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'status' => 'running',
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(3),
        ]);

        $deptA = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $deptB = Department::create(['name' => 'Management Sciences', 'code' => 'MS']);
        $sport = Sport::create(['name' => 'Cricket', 'type' => 'team', 'category' => 'boys']);

        Winner::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'winner_department_id' => $deptA->id,
            'runner_up_department_id' => $deptB->id,
            'score_summary' => 'CS won by 15 runs',
        ]);

        $response = $this->get(route('ceremony'));

        $response->assertStatus(200);
        $response->assertSee('Grand Ceremony');
        $response->assertSee('KIU Gala 2026');
        $response->assertSee('Computer Science');
        $response->assertSee('Management Sciences');
        $response->assertSee('confetti-canvas');
        $response->assertSee('General Trophy');
        $response->assertSee('Sport Champions');
    }

    public function test_presentation_alias_route_works(): void
    {
        $edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'status' => 'running',
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(3),
        ]);

        $response = $this->get(route('presentation'));
        $response->assertStatus(200);
        $response->assertSee('Grand Ceremony');
    }

    public function test_ceremony_filters_by_edition_query_param(): void
    {
        $ed1 = Edition::create([
            'year' => 2025,
            'title' => 'KIU Gala 2025',
            'status' => 'completed',
            'start_date' => now()->subYear(),
            'end_date' => now()->subYear()->addDays(5),
        ]);

        $ed2 = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'status' => 'running',
            'start_date' => now()->subDays(2),
            'end_date' => now()->addDays(3),
        ]);

        $response = $this->get(route('ceremony', ['edition_id' => $ed1->id]));

        $response->assertStatus(200);
        $response->assertSee('KIU Gala 2025');
    }

    public function test_admin_dashboard_and_reports_contain_ceremony_link(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $responseDashboard = $this->actingAs($admin)->get(route('admin.dashboard'));
        $responseDashboard->assertStatus(200);
        $responseDashboard->assertSee(route('ceremony'));

        $responseReports = $this->actingAs($admin)->get(route('admin.reports.index'));
        $responseReports->assertStatus(200);
        $responseReports->assertSee(route('ceremony'));
        $responseReports->assertSee('Auditorium Closing Ceremony Presentation');
    }
}
