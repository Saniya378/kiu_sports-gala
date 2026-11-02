<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Result;
use App\Models\Sport;
use App\Models\Team;
use App\Models\Venue;
use App\Services\BracketService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TournamentBracketTest extends TestCase
{
    use RefreshDatabase;

    public function test_bracket_service_structures_knockout_stages_and_identifies_champion(): void
    {
        $edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'status' => 'running',
        ]);

        $sport = Sport::create(['name' => 'Volleyball', 'category' => 'boys']);

        $deptCs = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $deptBba = Department::create(['name' => 'Business Admin', 'code' => 'BBA']);
        $deptEe = Department::create(['name' => 'Electrical Eng', 'code' => 'EE']);
        $deptMe = Department::create(['name' => 'Mechanical Eng', 'code' => 'ME']);

        $teamCs = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $deptCs->id, 'name' => 'CS Spikers']);
        $teamBba = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $deptBba->id, 'name' => 'BBA Smashers']);
        $teamEe = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $deptEe->id, 'name' => 'EE Blasters']);
        $teamMe = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $deptMe->id, 'name' => 'ME Giants']);

        $venue = Venue::create(['name' => 'Main Gymnasium']);

        // Semi Final 1
        $sf1 = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $teamCs->id,
            'team2_id' => $teamEe->id,
            'venue_id' => $venue->id,
            'match_date' => '2026-11-05',
            'match_time' => '10:00:00',
            'round' => 'Semi Final 1',
            'status' => 'completed',
        ]);
        Result::create([
            'fixture_id' => $sf1->id,
            'team1_score' => '2 Sets',
            'team2_score' => '1 Set',
            'winner_team_id' => $teamCs->id,
        ]);

        // Semi Final 2
        $sf2 = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $teamBba->id,
            'team2_id' => $teamMe->id,
            'venue_id' => $venue->id,
            'match_date' => '2026-11-05',
            'match_time' => '12:00:00',
            'round' => 'Semi Final 2',
            'status' => 'completed',
        ]);
        Result::create([
            'fixture_id' => $sf2->id,
            'team1_score' => '2 Sets',
            'team2_score' => '0 Sets',
            'winner_team_id' => $teamBba->id,
        ]);

        // Final
        $final = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $teamCs->id,
            'team2_id' => $teamBba->id,
            'venue_id' => $venue->id,
            'match_date' => '2026-11-06',
            'match_time' => '15:00:00',
            'round' => 'Grand Final',
            'status' => 'completed',
        ]);
        Result::create([
            'fixture_id' => $final->id,
            'team1_score' => '3 Sets',
            'team2_score' => '1 Set',
            'winner_team_id' => $teamCs->id,
        ]);

        $service = new BracketService();
        $bracket = $service->getBracket($edition, $sport);

        $this->assertTrue($bracket['has_knockout']);
        $this->assertEquals(3, $bracket['total_matches']);
        $this->assertArrayHasKey('Semi Finals', $bracket['rounds']);
        $this->assertArrayHasKey('Final', $bracket['rounds']);
        $this->assertCount(2, $bracket['rounds']['Semi Finals']);
        $this->assertCount(1, $bracket['rounds']['Final']);

        $this->assertNotNull($bracket['champion']);
        $this->assertEquals($deptCs->id, $bracket['champion']['department']->id);
        $this->assertEquals('final_fixture', $bracket['champion']['source']);
    }

    public function test_public_bracket_page_renders_bracket_stages_and_teams(): void
    {
        $edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'status' => 'running',
        ]);

        $sport = Sport::create(['name' => 'Basketball', 'category' => 'boys']);

        $deptCs = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $deptBba = Department::create(['name' => 'Business Admin', 'code' => 'BBA']);

        $teamCs = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $deptCs->id, 'name' => 'CS Hoopers']);
        $teamBba = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $deptBba->id, 'name' => 'BBA Dunkers']);

        $venue = Venue::create(['name' => 'Sports Complex']);

        $final = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $teamCs->id,
            'team2_id' => $teamBba->id,
            'venue_id' => $venue->id,
            'match_date' => '2026-11-06',
            'match_time' => '16:00:00',
            'round' => 'Final',
            'status' => 'completed',
        ]);
        Result::create([
            'fixture_id' => $final->id,
            'team1_score' => '72',
            'team2_score' => '68',
            'winner_team_id' => $teamCs->id,
        ]);

        $response = $this->get(route('bracket', [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('Tournament Knockout Brackets');
        $response->assertSee('Final');
        $response->assertSee('CS Hoopers');
        $response->assertSee('BBA Dunkers');
        $response->assertSee('Tournament Champion');
        $response->assertSee('Computer Science');
    }

    public function test_bracket_page_shows_placeholder_when_no_knockout_matches(): void
    {
        $edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'status' => 'running',
        ]);

        $sport = Sport::create(['name' => 'Table Tennis', 'category' => 'girls']);

        $response = $this->get(route('bracket', [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
        ]));

        $response->assertStatus(200);
        $response->assertSee('No Bracket Matches Configured');
    }
}
