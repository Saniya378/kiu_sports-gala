<?php

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamsAndPlayersTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $scorer;
    private Edition $edition2025;
    private Edition $edition2026;
    private Sport $football;
    private Sport $cricket;
    private Department $cs;
    private Department $bba;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->scorer = User::factory()->create(['role' => 'scorer']);

        $this->edition2025 = Edition::create([
            'title' => 'KIU Gala 2025',
            'year' => 2025,
            'status' => 'completed',
        ]);

        $this->edition2026 = Edition::create([
            'title' => 'KIU Gala 2026',
            'year' => 2026,
            'status' => 'upcoming',
        ]);

        $this->football = Sport::create([
            'name' => 'Football',
            'category' => 'boys',
        ]);

        $this->cricket = Sport::create([
            'name' => 'Cricket',
            'category' => 'boys',
        ]);

        $this->cs = Department::create(['name' => 'Computer Science']);
        $this->bba = Department::create(['name' => 'Business Administration']);
    }

    public function test_scorer_is_forbidden_from_teams(): void
    {
        $this->actingAs($this->scorer)
            ->get(route('admin.teams.index'))
            ->assertStatus(403);
    }

    public function test_admin_can_filter_teams_by_edition_and_sport(): void
    {
        $team1 = Team::create([
            'edition_id' => $this->edition2025->id,
            'sport_id' => $this->football->id,
            'department_id' => $this->cs->id,
            'name' => 'CS 2025 Football',
            'status' => 'approved',
        ]);

        $team2 = Team::create([
            'edition_id' => $this->edition2026->id,
            'sport_id' => $this->cricket->id,
            'department_id' => $this->bba->id,
            'name' => 'BBA 2026 Cricket',
            'status' => 'approved',
        ]);

        // Filter by 2026 edition
        $response = $this->actingAs($this->admin)
            ->get(route('admin.teams.index', ['edition_id' => $this->edition2026->id]));
        $response->assertStatus(200);
        $response->assertSee('BBA 2026 Cricket');
        $response->assertDontSee('CS 2025 Football');

        // Filter by football sport
        $responseSport = $this->actingAs($this->admin)
            ->get(route('admin.teams.index', ['sport_id' => $this->football->id]));
        $responseSport->assertStatus(200);
        $responseSport->assertSee('CS 2025 Football');
        $responseSport->assertDontSee('BBA 2026 Cricket');
    }

    public function test_admin_can_create_team_and_enforces_composite_unique(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.teams.store'), [
                'edition_id' => $this->edition2026->id,
                'sport_id' => $this->football->id,
                'department_id' => $this->cs->id,
                'name' => 'CS Strikers',
                'status' => 'approved',
            ]);

        $team = Team::where('name', 'CS Strikers')->first();
        $this->assertNotNull($team);
        $response->assertRedirect(route('admin.teams.edit', $team));

        // Attempting to create duplicate for same edition, sport, and department fails
        $duplicateResponse = $this->actingAs($this->admin)
            ->post(route('admin.teams.store'), [
                'edition_id' => $this->edition2026->id,
                'sport_id' => $this->football->id,
                'department_id' => $this->cs->id,
                'name' => 'CS Strikers Duplicate',
                'status' => 'approved',
            ]);

        $duplicateResponse->assertSessionHasErrors('department_id');
    }

    public function test_admin_can_update_and_delete_team(): void
    {
        $team = Team::create([
            'edition_id' => $this->edition2026->id,
            'sport_id' => $this->football->id,
            'department_id' => $this->cs->id,
            'name' => 'Old Team Name',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.teams.update', $team), [
                'edition_id' => $this->edition2026->id,
                'sport_id' => $this->football->id,
                'department_id' => $this->cs->id,
                'name' => 'New Team Name',
                'status' => 'approved',
            ]);

        $response->assertRedirect(route('admin.teams.edit', $team));
        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'New Team Name',
            'status' => 'approved',
        ]);

        $deleteResponse = $this->actingAs($this->admin)
            ->delete(route('admin.teams.destroy', $team));
        $deleteResponse->assertRedirect(route('admin.teams.index'));
        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function test_admin_can_add_and_remove_players_inline(): void
    {
        $team = Team::create([
            'edition_id' => $this->edition2026->id,
            'sport_id' => $this->football->id,
            'department_id' => $this->cs->id,
            'name' => 'CS Strikers',
            'status' => 'approved',
        ]);

        // Add player
        $response = $this->actingAs($this->admin)
            ->post(route('admin.teams.players.store', $team), [
                'name' => 'Ali Ahmed',
                'reg_no' => 'KIU-2023-CS-001',
                'contact' => '0345-1234567',
            ]);

        $response->assertRedirect(route('admin.teams.edit', $team));
        $this->assertDatabaseHas('players', [
            'team_id' => $team->id,
            'name' => 'Ali Ahmed',
            'reg_no' => 'KIU-2023-CS-001',
        ]);

        $player = Player::where('name', 'Ali Ahmed')->first();

        // Remove player
        $deleteResponse = $this->actingAs($this->admin)
            ->delete(route('admin.teams.players.destroy', [$team, $player]));

        $deleteResponse->assertRedirect(route('admin.teams.edit', $team));
        $this->assertDatabaseMissing('players', [
            'id' => $player->id,
        ]);
    }

    public function test_copy_teams_from_previous_edition_skips_duplicates_and_does_not_copy_players(): void
    {
        // Source edition teams with a player
        $sourceTeam1 = Team::create([
            'edition_id' => $this->edition2025->id,
            'sport_id' => $this->football->id,
            'department_id' => $this->cs->id,
            'name' => 'CS Football 2025',
            'status' => 'approved',
        ]);

        Player::create([
            'team_id' => $sourceTeam1->id,
            'name' => 'Previous Player',
            'reg_no' => '2025-001',
        ]);

        $sourceTeam2 = Team::create([
            'edition_id' => $this->edition2025->id,
            'sport_id' => $this->cricket->id,
            'department_id' => $this->bba->id,
            'name' => 'BBA Cricket 2025',
            'status' => 'approved',
        ]);

        // Target edition already has team for CS Football (this should be skipped as duplicate)
        $existingTargetTeam = Team::create([
            'edition_id' => $this->edition2026->id,
            'sport_id' => $this->football->id,
            'department_id' => $this->cs->id,
            'name' => 'Existing CS Football 2026',
            'status' => 'approved',
        ]);

        // Run copy action
        $response = $this->actingAs($this->admin)
            ->post(route('admin.teams.copy'), [
                'source_edition_id' => $this->edition2025->id,
                'target_edition_id' => $this->edition2026->id,
            ]);

        $response->assertRedirect(route('admin.teams.index', ['edition_id' => $this->edition2026->id]));
        $response->assertSessionHas('success');

        // Target should now have BBA Cricket copied
        $copiedTeam = Team::where('edition_id', $this->edition2026->id)
            ->where('sport_id', $this->cricket->id)
            ->where('department_id', $this->bba->id)
            ->first();

        $this->assertNotNull($copiedTeam);
        $this->assertEquals('BBA Cricket 2025', $copiedTeam->name);

        // Crucial: Players should NOT be copied!
        $this->assertEquals(0, $copiedTeam->players()->count());

        // Existing team should still be there untouched
        $this->assertEquals(2, Team::where('edition_id', $this->edition2026->id)->count());
    }

    public function test_admin_can_add_player_with_photo_and_removes_file_on_delete(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $team = Team::create([
            'edition_id' => $this->edition2026->id,
            'sport_id' => $this->football->id,
            'department_id' => $this->cs->id,
            'name' => 'CS Football Stars',
            'status' => 'approved',
        ]);

        $photo = \Illuminate\Http\UploadedFile::fake()->image('player_ali.jpg', 300, 300);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.teams.players.store', $team), [
                'name' => 'Ali Haider',
                'reg_no' => '2023-CS-099',
                'photo' => $photo,
            ]);

        $response->assertRedirect(route('admin.teams.edit', $team));

        $player = Player::where('reg_no', '2023-CS-099')->first();
        $this->assertNotNull($player);
        $this->assertNotNull($player->photo);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($player->photo);
        $this->assertNotNull($player->photo_url);

        // Delete player -> photo should be removed from storage
        $deleteResponse = $this->actingAs($this->admin)
            ->delete(route('admin.teams.players.destroy', [$team, $player]));

        $deleteResponse->assertRedirect(route('admin.teams.edit', $team));
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($player->photo);
        $this->assertDatabaseMissing('players', ['id' => $player->id]);
    }

    public function test_admin_can_bulk_import_players_via_csv(): void
    {
        $team = Team::create([
            'edition_id' => $this->edition2026->id,
            'sport_id' => $this->football->id,
            'department_id' => $this->cs->id,
            'name' => 'CS Strikers XI',
            'status' => 'approved',
        ]);

        $csvContent = "Name,Registration Number,Contact\n"
                    . "Kamran Shah,2023-CS-001,0300-1111111\n"
                    . "Zubair Ahmed,2023-CS-002,0300-2222222\n"
                    . "Bilal Khan,2023-CS-003,0300-3333333\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('roster.csv', $csvContent);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.teams.players.import', $team), [
                'csv_file' => $file,
            ]);

        $response->assertRedirect(route('admin.teams.edit', $team));
        $response->assertSessionHas('success');

        $this->assertEquals(3, $team->players()->count());
        $this->assertDatabaseHas('players', ['name' => 'Kamran Shah', 'reg_no' => '2023-CS-001']);
        $this->assertDatabaseHas('players', ['name' => 'Zubair Ahmed', 'reg_no' => '2023-CS-002']);
        $this->assertDatabaseHas('players', ['name' => 'Bilal Khan', 'reg_no' => '2023-CS-003']);
    }

    public function test_admin_can_download_sample_csv(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.teams.players.sample-csv'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="kiu_team_players_template.csv"');
        $this->assertStringContainsString('Player Name,Registration Number,Contact', $response->getContent());
    }
}

