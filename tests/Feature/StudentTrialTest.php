<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Player;
use App\Models\Sport;
use App\Models\StudentTrial;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentTrialTest extends TestCase
{
    use RefreshDatabase;

    private Edition $edition;
    private Department $deptCs;
    private Department $deptBba;
    private Sport $sport;
    private User $csCoordinator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->edition = Edition::create(['year' => 2026, 'title' => 'KIU Gala 2026', 'status' => 'running']);
        $this->deptCs = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $this->deptBba = Department::create(['name' => 'Business Admin', 'code' => 'BBA']);
        $this->sport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);

        $this->csCoordinator = User::factory()->create([
            'role' => 'coordinator',
            'department_id' => $this->deptCs->id,
        ]);
    }

    public function test_public_can_view_trials_registration_form(): void
    {
        $response = $this->get(route('trials.create'));

        $response->assertStatus(200);
        $response->assertSee('Department Team Trials Registration');
        $response->assertSee('Computer Science');
        $response->assertSee('Cricket');
    }

    public function test_student_can_register_for_trials_and_view_slip(): void
    {
        $postData = [
            'edition_id' => $this->edition->id,
            'department_id' => $this->deptCs->id,
            'sport_id' => $this->sport->id,
            'student_name' => 'Zeeshan Ali',
            'reg_no' => 'KIU-2024-CS-015',
            'gender' => 'male',
            'contact' => '03551234567',
            'experience_level' => 'advanced',
            'playing_position' => 'Fast Bowler',
            'additional_notes' => 'District U-19 player',
        ];

        $response = $this->post(route('trials.store'), $postData);

        $trial = StudentTrial::where('reg_no', 'KIU-2024-CS-015')->first();
        $this->assertNotNull($trial);
        $this->assertEquals('Zeeshan Ali', $trial->student_name);
        $this->assertEquals('pending', $trial->status);

        $response->assertRedirect(route('trials.slip', $trial));

        // View slip
        $slipResponse = $this->get(route('trials.slip', $trial));
        $slipResponse->assertStatus(200);
        $slipResponse->assertSee('Zeeshan Ali');
        $slipResponse->assertSee('KIU-2024-CS-015');
        $slipResponse->assertSee('Official Department Trial Slip');
    }

    public function test_duplicate_trial_application_for_same_sport_is_rejected(): void
    {
        StudentTrial::create([
            'edition_id' => $this->edition->id,
            'department_id' => $this->deptCs->id,
            'sport_id' => $this->sport->id,
            'student_name' => 'Zeeshan Ali',
            'reg_no' => 'KIU-2024-CS-015',
            'gender' => 'male',
            'contact' => '03551234567',
            'experience_level' => 'advanced',
            'status' => 'pending',
        ]);

        $response = $this->post(route('trials.store'), [
            'edition_id' => $this->edition->id,
            'department_id' => $this->deptCs->id,
            'sport_id' => $this->sport->id,
            'student_name' => 'Zeeshan Ali Duplicate',
            'reg_no' => 'KIU-2024-CS-015',
            'gender' => 'male',
            'contact' => '03551234567',
            'experience_level' => 'intermediate',
        ]);

        $response->assertSessionHasErrors('reg_no');
    }

    public function test_coordinator_can_view_department_trials_and_update_status(): void
    {
        $csTrial = StudentTrial::create([
            'edition_id' => $this->edition->id,
            'department_id' => $this->deptCs->id,
            'sport_id' => $this->sport->id,
            'student_name' => 'CS Athlete',
            'reg_no' => 'CS-001',
            'gender' => 'male',
            'contact' => '03551111111',
            'experience_level' => 'intermediate',
            'status' => 'pending',
        ]);

        $bbaTrial = StudentTrial::create([
            'edition_id' => $this->edition->id,
            'department_id' => $this->deptBba->id,
            'sport_id' => $this->sport->id,
            'student_name' => 'BBA Athlete',
            'reg_no' => 'BBA-001',
            'gender' => 'male',
            'contact' => '03552222222',
            'experience_level' => 'intermediate',
            'status' => 'pending',
        ]);

        // CS Coordinator visits trials index
        $response = $this->actingAs($this->csCoordinator)->get(route('coordinator.trials.index'));
        $response->assertStatus(200);
        $response->assertSee('CS Athlete');
        $response->assertDontSee('BBA Athlete'); // isolated to CS department

        // Update status to shortlisted
        $statusResponse = $this->actingAs($this->csCoordinator)->patch(route('coordinator.trials.status', $csTrial), [
            'status' => 'shortlisted',
            'coordinator_notes' => 'Call for ground trial on Tuesday',
        ]);

        $statusResponse->assertRedirect();
        $this->assertEquals('shortlisted', $csTrial->fresh()->status);
    }

    public function test_coordinator_can_draft_selected_trialist_into_squad(): void
    {
        $trial = StudentTrial::create([
            'edition_id' => $this->edition->id,
            'department_id' => $this->deptCs->id,
            'sport_id' => $this->sport->id,
            'student_name' => 'Draft Star',
            'reg_no' => 'CS-STAR-77',
            'gender' => 'male',
            'contact' => '03557777777',
            'experience_level' => 'varsity',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->csCoordinator)->post(route('coordinator.trials.draft', $trial));

        $response->assertRedirect();
        $this->assertEquals('selected', $trial->fresh()->status);

        // Player must be drafted into team roster
        $player = Player::where('reg_no', 'CS-STAR-77')->first();
        $this->assertNotNull($player);
        $this->assertEquals('Draft Star', $player->name);
        $this->assertEquals($this->deptCs->id, $player->team->department_id);
    }
}

