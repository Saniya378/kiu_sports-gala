<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\MatchEvent;
use App\Models\Player;
use App\Models\Result;
use App\Models\Sport;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_can_view_leaderboard_with_mvp_golden_boot_and_purple_cap(): void
    {
        $edition = Edition::create(['year' => 2026, 'title' => 'KIU Gala 2026', 'status' => 'running']);
        $sportFootball = Sport::create(['name' => 'Football', 'category' => 'boys']);
        $sportCricket = Sport::create(['name' => 'Cricket', 'category' => 'boys']);

        $deptCs = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $deptBba = Department::create(['name' => 'Business Admin', 'code' => 'BBA']);

        $fTeam1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sportFootball->id, 'department_id' => $deptCs->id, 'name' => 'CS FC']);
        $fTeam2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sportFootball->id, 'department_id' => $deptBba->id, 'name' => 'BBA FC']);

        $cTeam1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sportCricket->id, 'department_id' => $deptCs->id, 'name' => 'CS XI']);
        $cTeam2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sportCricket->id, 'department_id' => $deptBba->id, 'name' => 'BBA XI']);

        $striker = Player::create(['team_id' => $fTeam1->id, 'name' => 'Ali Striker', 'reg_no' => 'CS-101']);
        $bowler = Player::create(['team_id' => $cTeam1->id, 'name' => 'Babar Bowler', 'reg_no' => 'CS-102']);

        // Football match
        $fFixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sportFootball->id,
            'team1_id' => $fTeam1->id,
            'team2_id' => $fTeam2->id,
            'status' => 'completed',
        ]);
        Result::create([
            'fixture_id' => $fFixture->id,
            'team1_score' => 2,
            'team2_score' => 1,
            'winner_team_id' => $fTeam1->id,
            'player_of_the_match_id' => $striker->id,
        ]);
        MatchEvent::create([
            'fixture_id' => $fFixture->id,
            'team_id' => $fTeam1->id,
            'player_id' => $striker->id,
            'minute' => "23'",
            'event_type' => 'goal',
            'title' => 'Top corner goal',
        ]);

        // Cricket match
        $cFixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sportCricket->id,
            'team1_id' => $cTeam1->id,
            'team2_id' => $cTeam2->id,
            'status' => 'completed',
        ]);
        Result::create([
            'fixture_id' => $cFixture->id,
            'team1_score' => 140,
            'team2_score' => 110,
            'winner_team_id' => $cTeam1->id,
            'player_of_the_match_id' => $bowler->id,
        ]);
        MatchEvent::create([
            'fixture_id' => $cFixture->id,
            'team_id' => $cTeam1->id,
            'player_id' => $bowler->id,
            'minute' => 'Over 4.2',
            'event_type' => 'wicket',
            'title' => 'Clean bowled',
        ]);

        $response = $this->get(route('leaderboard'));

        $response->assertStatus(200);
        $response->assertSee('Stats &amp; Player Leaderboards', false);
        $response->assertSee('Gala MVP Award');
        $response->assertSee('Golden Boot');
        $response->assertSee('Purple Cap');
        $response->assertSee('Ali Striker');
        $response->assertSee('Babar Bowler');
    }

    public function test_public_can_view_individual_athlete_profile_and_stats(): void
    {
        $edition = Edition::create(['year' => 2026, 'title' => 'KIU Gala 2026', 'status' => 'running']);
        $sport = Sport::create(['name' => 'Football', 'category' => 'boys']);
        $dept = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $oppDept = Department::create(['name' => 'Engineering', 'code' => 'ENG']);
        $team = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept->id, 'name' => 'CS FC']);
        $oppTeam = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $oppDept->id, 'name' => 'Eng FC']);

        $player = Player::create([
            'team_id' => $team->id,
            'name' => 'Karim Star',
            'reg_no' => 'KIU-CS-2023-99',
        ]);

        $fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team->id,
            'team2_id' => $oppTeam->id,
            'status' => 'completed',
        ]);

        Result::create([
            'fixture_id' => $fixture->id,
            'team1_score' => 3,
            'team2_score' => 0,
            'winner_team_id' => $team->id,
            'player_of_the_match_id' => $player->id,
        ]);

        MatchEvent::create([
            'fixture_id' => $fixture->id,
            'team_id' => $team->id,
            'player_id' => $player->id,
            'minute' => "15'",
            'event_type' => 'goal',
            'title' => 'Opening Goal',
        ]);

        $response = $this->get(route('players.profile', $player));

        $response->assertStatus(200);
        $response->assertSee('Karim Star');
        $response->assertSee('KIU-CS-2023-99');
        $response->assertSee('Computer Science');
        $response->assertSee('Opening Goal');
        $response->assertSee('Player of Match');
        $response->assertSee('Official Digital ID Card');
    }
}
