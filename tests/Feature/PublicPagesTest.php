<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Edition;
use App\Models\EditionPhoto;
use App\Models\Fixture;
use App\Models\MatchPhoto;
use App\Models\News;
use App\Models\Player;
use App\Models\Result;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use App\Models\Winner;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_running_edition_and_todays_matches(): void
    {
        $runningEdition = Edition::create([
            'title' => 'KIU Gala 2026',
            'year' => 2026,
            'status' => 'running',
            'start_date' => Carbon::now()->subDays(2),
            'end_date' => Carbon::now()->addDays(5),
            'description' => 'The ultimate university championship',
        ]);

        $dept1 = Department::create(['name' => 'Computer Science']);
        $dept2 = Department::create(['name' => 'Physics']);
        $sport = Sport::create(['name' => 'Cricket', 'category' => 'boys', 'rules' => 'T20 Rules']);
        $venue = Venue::create(['name' => 'Main Ground']);

        $team1 = Team::create([
            'edition_id' => $runningEdition->id,
            'sport_id' => $sport->id,
            'department_id' => $dept1->id,
            'name' => 'CS Strikers',
            'status' => 'approved',
        ]);

        $team2 = Team::create([
            'edition_id' => $runningEdition->id,
            'sport_id' => $sport->id,
            'department_id' => $dept2->id,
            'name' => 'Physics Titans',
            'status' => 'approved',
        ]);

        $todayFixture = Fixture::create([
            'edition_id' => $runningEdition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'venue_id' => $venue->id,
            'match_date' => Carbon::today()->toDateString(),
            'match_time' => '10:00:00',
            'round' => 'Quarter Final',
            'status' => 'live',
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('KIU Gala 2026');
        $response->assertSee('CS Strikers');
        $response->assertSee('Physics Titans');
        $response->assertSee('Quarter Final');
        $response->assertSee('Live');
    }

    public function test_home_page_shows_next_gala_coming_soon_when_not_running(): void
    {
        $completedEdition = Edition::create([
            'title' => 'KIU Gala 2025',
            'year' => 2025,
            'status' => 'completed',
            'start_date' => Carbon::now()->subYear(),
            'end_date' => Carbon::now()->subYear()->addDays(7),
        ]);

        $upcomingEdition = Edition::create([
            'title' => 'KIU Gala 2027',
            'year' => 2027,
            'status' => 'upcoming',
        ]);

        $dept1 = Department::create(['name' => 'Earth Sciences']);
        $dept2 = Department::create(['name' => 'Chemistry']);
        $sport = Sport::create(['name' => 'Football', 'category' => 'boys']);

        Winner::create([
            'edition_id' => $completedEdition->id,
            'sport_id' => $sport->id,
            'winner_department_id' => $dept1->id,
            'runner_up_department_id' => $dept2->id,
            'final_score' => '2 - 1',
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Next Gala Coming Soon');
        $response->assertSee('Highlights of KIU Gala 2025');
        $response->assertSee('Earth Sciences');
    }

    public function test_todays_matches_page_shows_only_running_edition_matches_for_today(): void
    {
        $runningEdition = Edition::create([
            'title' => 'KIU Gala 2026',
            'year' => 2026,
            'status' => 'running',
        ]);

        $otherEdition = Edition::create([
            'title' => 'KIU Gala 2025',
            'year' => 2025,
            'status' => 'completed',
        ]);

        $dept1 = Department::create(['name' => 'CS']);
        $dept2 = Department::create(['name' => 'BBA']);
        $sportCricket = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $sportFootball = Sport::create(['name' => 'Football', 'category' => 'boys']);

        $t1 = Team::create(['edition_id' => $runningEdition->id, 'sport_id' => $sportCricket->id, 'department_id' => $dept1->id, 'name' => 'CS Cricket']);
        $t2 = Team::create(['edition_id' => $runningEdition->id, 'sport_id' => $sportCricket->id, 'department_id' => $dept2->id, 'name' => 'BBA Cricket']);
        $t3 = Team::create(['edition_id' => $runningEdition->id, 'sport_id' => $sportFootball->id, 'department_id' => $dept1->id, 'name' => 'CS Football']);
        $t4 = Team::create(['edition_id' => $runningEdition->id, 'sport_id' => $sportFootball->id, 'department_id' => $dept2->id, 'name' => 'BBA Football']);

        // Today match in running edition (Cricket)
        $todayFixture1 = Fixture::create([
            'edition_id' => $runningEdition->id,
            'sport_id' => $sportCricket->id,
            'team1_id' => $t1->id,
            'team2_id' => $t2->id,
            'match_date' => Carbon::today()->toDateString(),
            'status' => 'scheduled',
        ]);

        // Today match in running edition (Football)
        $todayFixture2 = Fixture::create([
            'edition_id' => $runningEdition->id,
            'sport_id' => $sportFootball->id,
            'team1_id' => $t3->id,
            'team2_id' => $t4->id,
            'match_date' => Carbon::today()->toDateString(),
            'status' => 'live',
        ]);

        // Tomorrow match in running edition - should NOT appear
        $futureFixture = Fixture::create([
            'edition_id' => $runningEdition->id,
            'sport_id' => $sportCricket->id,
            'team1_id' => $t1->id,
            'team2_id' => $t2->id,
            'match_date' => Carbon::tomorrow()->toDateString(),
            'status' => 'scheduled',
        ]);

        // All today matches
        $response = $this->get(route('today'));
        $response->assertStatus(200);
        $response->assertSee('CS Cricket');
        $response->assertSee('CS Football');

        // Filter by Cricket only
        $filterResponse = $this->get(route('today', ['sport_id' => $sportCricket->id]));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('CS Cricket');
        $filterResponse->assertDontSee('CS Football');
    }

    public function test_schedule_page_defaults_to_latest_edition_and_filters_fixtures(): void
    {
        $oldEdition = Edition::create(['title' => 'Gala 2024', 'year' => 2024, 'status' => 'completed']);
        $latestEdition = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);

        $dept1 = Department::create(['name' => 'Math']);
        $dept2 = Department::create(['name' => 'Bio']);
        $sport = Sport::create(['name' => 'Volleyball', 'category' => 'girls']);
        $venue = Venue::create(['name' => 'Indoor Gymnasium']);

        $team1 = Team::create(['edition_id' => $latestEdition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'Math Spikers']);
        $team2 = Team::create(['edition_id' => $latestEdition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'Bio Spikers']);

        $fixture = Fixture::create([
            'edition_id' => $latestEdition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'venue_id' => $venue->id,
            'match_date' => Carbon::now()->addDays(3)->toDateString(),
            'round' => 'Semi Final',
            'status' => 'scheduled',
        ]);

        // Default request without edition_id should default to Gala 2026 (latest by year)
        $response = $this->get(route('schedule'));
        $response->assertStatus(200);
        $response->assertSee('Gala 2026');
        $response->assertSee('Math Spikers');
        $response->assertSee('Bio Spikers');
        $response->assertSee('Indoor Gymnasium');
    }

    public function test_match_detail_page_displays_teams_score_venue_round_and_photos(): void
    {
        $edition = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);
        $dept1 = Department::create(['name' => 'Economics']);
        $dept2 = Department::create(['name' => 'Education']);
        $sport = Sport::create(['name' => 'Basketball', 'category' => 'boys']);
        $venue = Venue::create(['name' => 'Basketball Court A']);

        $team1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'Econ Bulls']);
        $team2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'Edu Warriors']);

        $player1 = Player::create(['team_id' => $team1->id, 'name' => 'Ali Khan', 'reg_no' => 'KIU-2023-EC-01']);
        $player2 = Player::create(['team_id' => $team2->id, 'name' => 'Zubair Shah', 'reg_no' => 'KIU-2023-ED-09']);

        $fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'venue_id' => $venue->id,
            'match_date' => Carbon::today()->toDateString(),
            'match_time' => '14:30:00',
            'round' => 'Grand Final',
            'status' => 'completed',
        ]);

        Result::create([
            'fixture_id' => $fixture->id,
            'team1_score' => 64,
            'team2_score' => 58,
            'winner_team_id' => $team1->id,
            'summary' => 'Thrilling final with buzzer beater finish',
        ]);

        $user = User::factory()->create(['role' => 'admin']);
        $photo = MatchPhoto::create([
            'fixture_id' => $fixture->id,
            'image' => 'matches/action_photo_test.jpg',
            'caption' => 'Winning slam dunk in final quarter',
            'uploaded_by' => $user->id,
        ]);

        $response = $this->get(route('matches.show', $fixture));

        $response->assertStatus(200);
        $response->assertSee('Econ Bulls');
        $response->assertSee('Edu Warriors');
        $response->assertSee('64');
        $response->assertSee('58');
        $response->assertSee('Basketball Court A');
        $response->assertSee('Grand Final');
        $response->assertSee('Ali Khan');
        $response->assertSee('Zubair Shah');
        $response->assertSee('Winning slam dunk in final quarter');
    }

    public function test_results_and_points_table_page_calculates_standings_and_completed_matches(): void
    {
        $edition = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);
        $dept1 = Department::create(['name' => 'IT Department']);
        $dept2 = Department::create(['name' => 'Media Studies']);
        $sport = Sport::create(['name' => 'Futsal', 'category' => 'boys']);
        $venue = Venue::create(['name' => 'Futsal Arena']);

        $team1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'IT Futsal']);
        $team2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'Media Futsal']);

        $fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'venue_id' => $venue->id,
            'match_date' => Carbon::yesterday()->toDateString(),
            'round' => 'Group Match',
            'status' => 'completed',
        ]);

        Result::create([
            'fixture_id' => $fixture->id,
            'team1_score' => 4,
            'team2_score' => 1,
            'winner_team_id' => $team1->id,
            'summary' => 'Dominant attacking performance by IT',
        ]);

        // Default call without params should load latest edition & first sport
        $response = $this->get(route('results'));

        $response->assertStatus(200);
        $response->assertSee('Results &amp; Points Table', false);
        $response->assertSee('Points Table: Futsal');
        $response->assertSee('IT Futsal');
        $response->assertSee('Media Futsal');
        // Team 1 won, should have 3 points
        $response->assertSee('Leader');
        // Match results card
        $response->assertSee('Completed Match Results');
        $response->assertSee('4');
        $response->assertSee('1');
        $response->assertSee('Winner:');
    }

    public function test_archive_page_displays_selected_year_sports_winners_results_and_photos(): void
    {
        $edition2024 = Edition::create([
            'title' => 'KIU Gala 2024',
            'year' => 2024,
            'status' => 'completed',
            'description' => 'Memorable 2024 games archive',
        ]);

        $edition2025 = Edition::create([
            'title' => 'KIU Gala 2025',
            'year' => 2025,
            'status' => 'completed',
            'description' => 'Historic silver jubilee edition',
        ]);

        $dept1 = Department::create(['name' => 'Computer Science']);
        $dept2 = Department::create(['name' => 'Business Administration']);
        $sport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $venue = Venue::create(['name' => 'Gilgit Stadium']);

        // Winner record for 2024
        Winner::create([
            'edition_id' => $edition2024->id,
            'sport_id' => $sport->id,
            'winner_department_id' => $dept1->id,
            'runner_up_department_id' => $dept2->id,
            'final_score' => '156/4 vs 120/8',
        ]);

        // Teams & Fixture with result for 2024
        $team1 = Team::create(['edition_id' => $edition2024->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'CS 2024']);
        $team2 = Team::create(['edition_id' => $edition2024->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'BBA 2024']);

        $fixture = Fixture::create([
            'edition_id' => $edition2024->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'venue_id' => $venue->id,
            'match_date' => '2024-10-15',
            'round' => 'Final',
            'status' => 'completed',
        ]);

        Result::create([
            'fixture_id' => $fixture->id,
            'team1_score' => 156,
            'team2_score' => 120,
            'winner_team_id' => $team1->id,
            'summary' => 'Spectacular final win by CS',
        ]);

        // Edition photo
        EditionPhoto::create([
            'edition_id' => $edition2024->id,
            'sport_id' => $sport->id,
            'image' => 'editions/trophy_ceremony_2024.jpg',
            'caption' => '2024 Trophy Lifting Ceremony',
        ]);

        // Request archive for year 2024
        $response = $this->get(route('archive', ['year' => 2024]));

        $response->assertStatus(200);
        $response->assertSee('KIU Gala 2024');
        $response->assertSee('Edition 2024');
        // Sports
        $response->assertSee('Cricket');
        // Winners
        $response->assertSee('Computer Science');
        $response->assertSee('Business Administration');
        $response->assertSee('156/4 vs 120/8');
        // Results
        $response->assertSee('CS 2024');
        $response->assertSee('BBA 2024');
        $response->assertSee('156');
        $response->assertSee('120');
        // Photos
        $response->assertSee('2024 Trophy Lifting Ceremony');
    }

    public function test_hall_of_fame_page_lists_winners_and_runner_ups_per_sport(): void
    {
        $edition2024 = Edition::create(['title' => 'Gala 2024', 'year' => 2024, 'status' => 'completed']);
        $edition2025 = Edition::create(['title' => 'Gala 2025', 'year' => 2025, 'status' => 'completed']);

        $deptCS = Department::create(['name' => 'Computer Science']);
        $deptMath = Department::create(['name' => 'Mathematics']);
        $deptBio = Department::create(['name' => 'Biological Sciences']);

        $sportFootball = Sport::create(['name' => 'Football', 'category' => 'boys']);
        $sportBadminton = Sport::create(['name' => 'Badminton', 'category' => 'girls']);

        // 2024 Football: CS (champion) vs Math (runner up)
        Winner::create([
            'edition_id' => $edition2024->id,
            'sport_id' => $sportFootball->id,
            'winner_department_id' => $deptCS->id,
            'runner_up_department_id' => $deptMath->id,
            'final_score' => '3 - 1',
        ]);

        // 2025 Football: CS (champion) vs Bio (runner up)
        Winner::create([
            'edition_id' => $edition2025->id,
            'sport_id' => $sportFootball->id,
            'winner_department_id' => $deptCS->id,
            'runner_up_department_id' => $deptBio->id,
            'final_score' => '2 - 0',
        ]);

        // 2025 Badminton: Math (champion) vs CS (runner up)
        Winner::create([
            'edition_id' => $edition2025->id,
            'sport_id' => $sportBadminton->id,
            'winner_department_id' => $deptMath->id,
            'runner_up_department_id' => $deptCS->id,
            'final_score' => '21-18, 21-19',
        ]);

        $response = $this->get(route('hall-of-fame'));

        $response->assertStatus(200);
        $response->assertSee('KIU Sports Hall of Fame');
        // Football sport section
        $response->assertSee('Football');
        $response->assertSee('3 - 1');
        $response->assertSee('2 - 0');
        // Badminton sport section
        $response->assertSee('Badminton');
        $response->assertSee('21-18, 21-19');
        // Departments
        $response->assertSee('Computer Science');
        $response->assertSee('Mathematics');
        $response->assertSee('Biological Sciences');
        // All-Time Leaderboard
        $response->assertSee('All-Time Department Championship Leaderboard');
    }

    public function test_gallery_page_groups_photos_by_edition_and_renders_lightbox(): void
    {
        $edition2025 = Edition::create(['title' => 'Gala 2025', 'year' => 2025, 'status' => 'completed']);
        $edition2026 = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);

        $dept1 = Department::create(['name' => 'Physics']);
        $dept2 = Department::create(['name' => 'Chemistry']);
        $sport = Sport::create(['name' => 'Table Tennis', 'category' => 'boys']);

        // Edition Photo for 2025
        EditionPhoto::create([
            'edition_id' => $edition2025->id,
            'sport_id' => $sport->id,
            'image' => 'editions/gala_2025_opening.jpg',
            'caption' => 'Grand Opening Parade 2025',
        ]);

        // Match Photo for 2026
        $t1 = Team::create(['edition_id' => $edition2026->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'Phys T1']);
        $t2 = Team::create(['edition_id' => $edition2026->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'Chem T2']);
        $fixture = Fixture::create([
            'edition_id' => $edition2026->id,
            'sport_id' => $sport->id,
            'team1_id' => $t1->id,
            'team2_id' => $t2->id,
            'status' => 'completed',
        ]);

        $user = User::factory()->create(['role' => 'admin']);
        MatchPhoto::create([
            'fixture_id' => $fixture->id,
            'image' => 'matches/tt_final_rally_2026.jpg',
            'caption' => 'Epic Table Tennis Final Rally',
            'uploaded_by' => $user->id,
        ]);

        // Test all gallery view
        $response = $this->get(route('gallery'));

        $response->assertStatus(200);
        $response->assertSee('Gala Photo Gallery');
        $response->assertSee('Gala 2025');
        $response->assertSee('Grand Opening Parade 2025');
        $response->assertSee('Gala 2026');
        $response->assertSee('Epic Table Tennis Final Rally');
        // Lightbox modal component
        $response->assertSee('openLightbox');
        $response->assertSee('closeLightbox');

        // Test filtering by year 2025
        $filterResponse = $this->get(route('gallery', ['year' => 2025]));
        $filterResponse->assertStatus(200);
        $filterResponse->assertSee('Grand Opening Parade 2025');
        $filterResponse->assertDontSee('Epic Table Tennis Final Rally');
    }

    public function test_public_can_view_news_list_and_detail_page(): void
    {
        $edition = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);

        $article = News::create([
            'title' => 'Registration Open for All Departmental Teams',
            'body' => 'All departmental captains are requested to submit team rosters before Friday.',
            'edition_id' => $edition->id,
            'published_at' => Carbon::now()->subHour(),
        ]);

        // Unreleased future draft - should not appear in public listing
        $futureArticle = News::create([
            'title' => 'Secret Future Announcement',
            'body' => 'Should not be visible yet.',
            'published_at' => Carbon::now()->addDays(2),
        ]);

        $response = $this->get(route('news'));
        $response->assertStatus(200);
        $response->assertSee('Gala Bulletins');
        $response->assertSee('Registration Open for All Departmental Teams');
        $response->assertDontSee('Secret Future Announcement');

        // View detail page
        $detailResponse = $this->get(route('news.show', $article));
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Registration Open for All Departmental Teams');
        $detailResponse->assertSee('All departmental captains are requested to submit team rosters');
    }

    public function test_public_can_view_teams_page(): void
    {
        $edition = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);
        $dept = Department::create(['name' => 'Computer Science']);
        $sport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);

        $team = Team::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'department_id' => $dept->id,
            'name' => 'CS Strikers',
            'status' => 'approved',
        ]);

        Player::create(['team_id' => $team->id, 'name' => 'Adnan Baig', 'reg_no' => 'KIU-CS-2026']);

        $response = $this->get(route('teams.public'));
        $response->assertStatus(200);
        $response->assertSee('Participating Teams');
        $response->assertSee('CS Strikers');
        $response->assertSee('Adnan Baig');
    }

    public function test_public_can_view_about_and_contact_page(): void
    {
        $response = $this->get(route('about'));
        $response->assertStatus(200);
        $response->assertSee('About the Annual Sports Gala');
        $response->assertSee('Directorate of Sports');
        $response->assertSee('sports@kiu.edu.pk');

        $contactResponse = $this->get(route('contact'));
        $contactResponse->assertStatus(200);
        $contactResponse->assertSee('Contact Directorate');
    }
}
