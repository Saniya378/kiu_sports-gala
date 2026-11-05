<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Sport;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentFollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_follow_and_unfollow_department(): void
    {
        $dept = Department::create(['name' => 'Computer Science']);

        // Follow department
        $response = $this->postJson(route('api.department.follow'), [
            'department_id' => $dept->id,
        ]);

        $response->assertOk()
            ->assertJson([
                'success' => true,
                'followed_department_id' => $dept->id,
                'department_name' => 'Computer Science',
            ]);

        $this->assertEquals($dept->id, session('followed_department_id'));

        // Query followed department
        $queryResponse = $this->getJson(route('api.department.followed'));
        $queryResponse->assertOk()
            ->assertJson([
                'followed_department_id' => $dept->id,
                'department_name' => 'Computer Science',
            ]);

        // Unfollow department
        $unfollowResponse = $this->postJson(route('api.department.follow'), [
            'department_id' => null,
        ]);

        $unfollowResponse->assertOk()
            ->assertJson([
                'success' => true,
                'followed_department_id' => null,
            ]);

        $this->assertNull(session('followed_department_id'));
    }

    public function test_today_page_displays_department_follow_toolbar(): void
    {
        $dept1 = Department::create(['name' => 'Computer Science']);
        $dept2 = Department::create(['name' => 'Earth Sciences']);

        $edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(5),
            'status' => 'running',
        ]);

        $response = $this->withSession(['followed_department_id' => $dept1->id])
            ->get(route('today'));

        $response->assertOk();
        $response->assertSee('Follow Your Department');
        $response->assertSee('Enable Live Alerts');
        $response->assertSee('Computer Science');
        $response->assertSee('Earth Sciences');
    }

    public function test_live_scores_api_contains_department_ids_for_client_matching(): void
    {
        $dept1 = Department::create(['name' => 'Physics']);
        $dept2 = Department::create(['name' => 'Chemistry']);

        $edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(5),
            'status' => 'running',
        ]);

        $sport = Sport::create([
            'name' => 'Volleyball',
            'category' => 'boys',
            'scoring_type' => 'points',
        ]);

        $team1 = Team::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'department_id' => $dept1->id,
            'name' => 'Physics Spikers',
        ]);

        $team2 = Team::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'department_id' => $dept2->id,
            'name' => 'Chemistry Spikers',
        ]);

        $fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'match_date' => Carbon::today(),
            'match_time' => '14:00',
            'status' => 'live',
            'round' => 'Group Stage',
        ]);

        $response = $this->getJson(route('api.live-scores.index'));

        $response->assertOk()
            ->assertJsonPath('matches.0.team1_department_id', $dept1->id)
            ->assertJsonPath('matches.0.team2_department_id', $dept2->id)
            ->assertJsonPath('matches.0.team1_name', 'Physics Spikers');
    }
}

