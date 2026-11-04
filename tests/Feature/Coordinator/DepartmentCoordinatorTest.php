<?php

namespace Tests\Feature\Coordinator;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DepartmentCoordinatorTest extends TestCase
{
    use RefreshDatabase;

    private Department $deptCS;
    private Department $deptBBA;
    private Edition $edition;
    private Sport $cricket;
    private User $csCoordinator;
    private User $admin;
    private User $scorer;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->deptCS = Department::create(['name' => 'Computer Science']);
        $this->deptBBA = Department::create(['name' => 'Business Administration']);

        $this->edition = Edition::create([
            'title' => 'KIU Gala 2026',
            'year' => 2026,
            'status' => 'running',
        ]);

        $this->cricket = Sport::create([
            'name' => 'Cricket Men',
            'category' => 'boys',
            'scoring_type' => 'runs',
        ]);

        $this->csCoordinator = User::factory()->create([
            'name' => 'CS Coordinator',
            'email' => 'cs.coordinator@kiu.test',
            'role' => 'coordinator',
            'department_id' => $this->deptCS->id,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@kiu.test',
            'role' => 'admin',
        ]);

        $this->scorer = User::factory()->create([
            'name' => 'Ground Scorer',
            'email' => 'scorer@kiu.test',
            'role' => 'scorer',
        ]);
    }

    public function test_guest_is_redirected_from_coordinator_portal(): void
    {
        $response = $this->get(route('coordinator.teams.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_scorer_is_forbidden_from_coordinator_portal(): void
    {
        $response = $this->actingAs($this->scorer)->get(route('coordinator.teams.index'));
        $response->assertStatus(403);
    }

    public function test_coordinator_dashboard_redirect_works(): void
    {
        $response = $this->actingAs($this->csCoordinator)->get(route('dashboard'));
        $response->assertRedirect(route('coordinator.teams.index'));
    }

    public function test_coordinator_can_view_portal_and_their_department_squads(): void
    {
        // CS Team
        $csTeam = Team::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricket->id,
            'department_id' => $this->deptCS->id,
            'name' => 'CS Cricket Strikers',
            'status' => 'pending',
        ]);

        // BBA Team (different department)
        $bbaTeam = Team::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricket->id,
            'department_id' => $this->deptBBA->id,
            'name' => 'BBA Cricket Kings',
            'status' => 'approved',
        ]);

        $response = $this->actingAs($this->csCoordinator)->get(route('coordinator.teams.index'));
        $response->assertOk();
        $response->assertSee('Computer Science - Sports Coordinator Portal');
        $response->assertSee('CS Cricket Strikers');
        $response->assertDontSee('BBA Cricket Kings'); // Isolated to CS dept
    }

    public function test_coordinator_can_register_new_squad_which_defaults_to_pending(): void
    {
        $response = $this->actingAs($this->csCoordinator)->post(route('coordinator.teams.store'), [
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricket->id,
            'name' => 'Computer Science Cricket XI',
        ]);

        $team = Team::where('name', 'Computer Science Cricket XI')->first();
        $this->assertNotNull($team);
        $this->assertEquals('pending', $team->status);
        $this->assertEquals($this->deptCS->id, $team->department_id);

        $response->assertRedirect(route('coordinator.teams.show', $team));
        $response->assertSessionHas('success');
    }

    public function test_coordinator_cannot_view_or_tamper_with_other_departments_squads(): void
    {
        $bbaTeam = Team::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricket->id,
            'department_id' => $this->deptBBA->id,
            'name' => 'BBA Warriors',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->csCoordinator)->get(route('coordinator.teams.show', $bbaTeam));
        $response->assertStatus(403);
    }

    public function test_coordinator_can_add_athlete_with_photo_and_remove_athlete(): void
    {
        $team = Team::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricket->id,
            'department_id' => $this->deptCS->id,
            'name' => 'CS Cricket XI',
            'status' => 'pending',
        ]);

        $photo = UploadedFile::fake()->image('athlete.jpg', 300, 300);

        $response = $this->actingAs($this->csCoordinator)
            ->post(route('coordinator.teams.players.store', $team), [
                'name' => 'Ali Raza',
                'reg_no' => 'KIU-2023-CS-001',
                'contact' => '0312-0000000',
                'photo' => $photo,
            ]);

        $response->assertRedirect(route('coordinator.teams.show', $team));
        $this->assertDatabaseHas('players', [
            'team_id' => $team->id,
            'name' => 'Ali Raza',
            'reg_no' => 'KIU-2023-CS-001',
        ]);

        $player = Player::where('reg_no', 'KIU-2023-CS-001')->first();
        $this->assertNotNull($player->photo);
        Storage::disk('public')->assertExists($player->photo);

        // Delete player
        $delResponse = $this->actingAs($this->csCoordinator)
            ->delete(route('coordinator.teams.players.destroy', [$team, $player]));

        $delResponse->assertRedirect(route('coordinator.teams.show', $team));
        $this->assertDatabaseMissing('players', ['id' => $player->id]);
        Storage::disk('public')->assertMissing($player->photo);
    }

    public function test_coordinator_can_bulk_import_athletes_via_csv(): void
    {
        $team = Team::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricket->id,
            'department_id' => $this->deptCS->id,
            'name' => 'CS Cricket XI',
            'status' => 'pending',
        ]);

        $csvContent = "Name,RegNo,Contact\nPlayer 1,KIU-01,0300\nPlayer 2,KIU-02,0301";
        $csvFile = UploadedFile::fake()->createWithContent('squad.csv', $csvContent);

        $response = $this->actingAs($this->csCoordinator)
            ->post(route('coordinator.teams.players.import', $team), [
                'csv_file' => $csvFile,
            ]);

        $response->assertRedirect(route('coordinator.teams.show', $team));
        $this->assertDatabaseHas('players', ['name' => 'Player 1', 'reg_no' => 'KIU-01']);
        $this->assertDatabaseHas('players', ['name' => 'Player 2', 'reg_no' => 'KIU-02']);
    }

    public function test_admin_can_approve_and_reject_teams_and_coordinator_can_resubmit(): void
    {
        $team = Team::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->cricket->id,
            'department_id' => $this->deptCS->id,
            'name' => 'CS Cricket XI',
            'status' => 'pending',
        ]);

        // 1. Admin rejects
        $rejectResponse = $this->actingAs($this->admin)->post(route('admin.teams.reject', $team), [
            'rejection_reason' => 'Need 11 players minimum',
        ]);
        $rejectResponse->assertSessionHas('success');
        $this->assertEquals('rejected', $team->fresh()->status);
        $this->assertEquals('Need 11 players minimum', $team->fresh()->rejection_reason);

        // 2. Coordinator resubmits
        $resubmitResponse = $this->actingAs($this->csCoordinator)
            ->post(route('coordinator.teams.resubmit', $team));
        $resubmitResponse->assertRedirect(route('coordinator.teams.show', $team));
        $this->assertEquals('pending', $team->fresh()->status);
        $this->assertNull($team->fresh()->rejection_reason);

        // 3. Admin approves
        $approveResponse = $this->actingAs($this->admin)->post(route('admin.teams.approve', $team));
        $approveResponse->assertSessionHas('success');
        $this->assertEquals('approved', $team->fresh()->status);
    }
}

