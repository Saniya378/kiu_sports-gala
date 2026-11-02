<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Player;
use App\Models\Sport;
use App\Models\Team;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerIdCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_qr_code_service_generates_valid_svg_string(): void
    {
        $service = new QrCodeService();
        $svg = $service->generateSvg('https://kiu.edu.pk/sports-gala');

        $this->assertStringContainsString('<svg', $svg);
        $this->assertStringContainsString('</svg>', $svg);
        $this->assertStringNotContainsString('<?xml', $svg);

        $dataUri = $service->generateDataUri('https://kiu.edu.pk/sports-gala');
        $this->assertStringStartsWith('data:image/svg+xml;base64,', $dataUri);
    }

    public function test_public_can_view_player_digital_pass_with_qr_code(): void
    {
        $edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'status' => 'running',
        ]);

        $sport = Sport::create(['name' => 'Football', 'category' => 'boys']);
        $department = Department::create(['name' => 'Computer Science', 'code' => 'CS']);

        $team = Team::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'department_id' => $department->id,
            'name' => 'CS Strikers',
        ]);

        $player = Player::create([
            'team_id' => $team->id,
            'name' => 'Ali Ahmed',
            'reg_no' => '2023-KIU-CS-042',
            'contact' => '0300-1234567',
        ]);

        $response = $this->get(route('players.card', $player));

        $response->assertStatus(200);
        $response->assertSee('Ali Ahmed');
        $response->assertSee('2023-KIU-CS-042');
        $response->assertSee('Computer Science');
        $response->assertSee('CS Strikers');
        $response->assertSee('Football');
        $response->assertSee('Officially Verified');
        $response->assertSee('Official Athlete Accreditation Pass');
        $response->assertSee('<svg', false);
    }

    public function test_public_verification_url_resolves_and_verifies_athlete(): void
    {
        $edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'status' => 'running',
        ]);

        $sport = Sport::create(['name' => 'Volleyball', 'category' => 'girls']);
        $department = Department::create(['name' => 'Biological Sciences', 'code' => 'BIO']);

        $team = Team::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'department_id' => $department->id,
            'name' => 'Bio Smashers',
        ]);

        $player = Player::create([
            'team_id' => $team->id,
            'name' => 'Fatima Zahra',
            'reg_no' => '2024-KIU-BIO-019',
        ]);

        $response = $this->get(route('players.verify', $player));

        $response->assertStatus(200);
        $response->assertSee('Fatima Zahra');
        $response->assertSee('2024-KIU-BIO-019');
        $response->assertSee('Biological Sciences');
        $response->assertSee('Officially Verified');
    }

    public function test_team_printable_id_cards_sheet_renders_all_athletes(): void
    {
        $edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'status' => 'running',
        ]);

        $sport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $department = Department::create(['name' => 'Business Administration', 'code' => 'BBA']);

        $team = Team::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'department_id' => $department->id,
            'name' => 'BBA Tigers',
        ]);

        Player::create(['team_id' => $team->id, 'name' => 'Kamran Khan', 'reg_no' => '2022-KIU-BBA-001']);
        Player::create(['team_id' => $team->id, 'name' => 'Bilal Tariq', 'reg_no' => '2022-KIU-BBA-002']);
        Player::create(['team_id' => $team->id, 'name' => 'Zubair Shah', 'reg_no' => '2022-KIU-BBA-003']);

        $response = $this->get(route('teams.id-cards', $team));

        $response->assertStatus(200);
        $response->assertSee('BBA Tigers');
        $response->assertSee('Squad Accreditation ID Cards');
        $response->assertSee('Kamran Khan');
        $response->assertSee('Bilal Tariq');
        $response->assertSee('Zubair Shah');
        $response->assertSee('2022-KIU-BBA-001');
        $response->assertSee('2022-KIU-BBA-002');
        $response->assertSee('2022-KIU-BBA-003');
    }

    public function test_public_teams_page_contains_pass_links(): void
    {
        $edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'status' => 'running',
        ]);

        $sport = Sport::create(['name' => 'Badminton', 'category' => 'boys']);
        $department = Department::create(['name' => 'Mathematics', 'code' => 'MTH']);

        $team = Team::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'department_id' => $department->id,
            'name' => 'MTH Smashers',
        ]);

        $player = Player::create([
            'team_id' => $team->id,
            'name' => 'Tariq Mehmood',
            'reg_no' => '2023-KIU-MTH-011',
        ]);

        $response = $this->get(route('teams.public'));

        $response->assertStatus(200);
        $response->assertSee(route('players.card', $player));
        $response->assertSee(route('teams.id-cards', $team));
    }
}

