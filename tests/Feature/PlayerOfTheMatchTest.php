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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerOfTheMatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_scorer_can_award_player_of_the_match(): void
    {
        $scorer = User::factory()->create(['role' => 'scorer']);
        $edition = Edition::create(['year' => 2026, 'title' => 'Gala 2026', 'status' => 'running']);
        $sport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'CS', 'code' => 'CS']);
        $dept2 = Department::create(['name' => 'BBA', 'code' => 'BBA']);

        $team1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'CS Thunder']);
        $team2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'BBA Hawks']);

        $player = Player::create([
            'team_id' => $team1->id,
            'name' => 'Daniyal Baig',
            'reg_no' => 'KIU-2024-CS-022',
        ]);

        $fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'status' => 'scheduled',
        ]);

        $response = $this->actingAs($scorer)->post(route('admin.score-entry.store'), [
            'fixture_id' => $fixture->id,
            'team1_score' => 170,
            'team2_score' => 135,
            'status' => 'completed',
            'player_of_the_match_id' => $player->id,
            'summary' => 'Heroic all-round performance with 65 runs and 3 wickets.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('results', [
            'fixture_id' => $fixture->id,
            'team1_score' => 170,
            'team2_score' => 135,
            'winner_team_id' => $team1->id,
            'player_of_the_match_id' => $player->id,
        ]);

        // Verify relationship on Result
        $result = Result::where('fixture_id', $fixture->id)->first();
        $this->assertNotNull($result->playerOfTheMatch);
        $this->assertEquals('Daniyal Baig', $result->playerOfTheMatch->name);
    }

    public function test_match_detail_page_displays_player_of_the_match_spotlight(): void
    {
        $edition = Edition::create(['year' => 2026, 'title' => 'Gala 2026', 'status' => 'running']);
        $sport = Sport::create(['name' => 'Football', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'Mathematics', 'code' => 'MTH']);
        $dept2 = Department::create(['name' => 'Biological Sciences', 'code' => 'BIO']);
        $team1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'Math Stars']);
        $team2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'Bio Tigers']);

        $player = Player::create([
            'team_id' => $team1->id,
            'name' => 'Rizwan Shah',
            'reg_no' => 'KIU-2023-MTH-005',
        ]);

        $fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'status' => 'completed',
        ]);

        Result::create([
            'fixture_id' => $fixture->id,
            'team1_score' => 2,
            'team2_score' => 1,
            'winner_team_id' => $team1->id,
            'player_of_the_match_id' => $player->id,
            'summary' => 'Scored decisive winning free-kick in stoppage time.',
        ]);

        $response = $this->get(route('matches.show', $fixture));

        $response->assertOk()
            ->assertSee('Player of the Match')
            ->assertSee('Rizwan Shah')
            ->assertSee('KIU-2023-MTH-005');
    }

    public function test_live_api_returns_player_of_the_match_details(): void
    {
        $edition = Edition::create(['year' => 2026, 'title' => 'Gala 2026', 'status' => 'running']);
        $sport = Sport::create(['name' => 'Volleyball', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'Physics', 'code' => 'PHY']);
        $dept2 = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $team1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'Phy Spikers']);
        $team2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'CS Spikers']);

        $player = Player::create([
            'team_id' => $team1->id,
            'name' => 'Ali Raza',
            'reg_no' => 'KIU-2022-PHY-019',
        ]);

        $fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'status' => 'completed',
        ]);

        Result::create([
            'fixture_id' => $fixture->id,
            'team1_score' => 25,
            'team2_score' => 21,
            'winner_team_id' => $team1->id,
            'player_of_the_match_id' => $player->id,
        ]);

        $response = $this->getJson(route('api.live-scores.show', $fixture));

        $response->assertOk()
            ->assertJsonPath('result.player_of_the_match.id', $player->id)
            ->assertJsonPath('result.player_of_the_match.name', 'Ali Raza')
            ->assertJsonPath('result.player_of_the_match.reg_no', 'KIU-2022-PHY-019');
    }
}
