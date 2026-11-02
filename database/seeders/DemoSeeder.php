<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\News;
use App\Models\Player;
use App\Models\Result;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use App\Models\Winner;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoSeeder extends Seeder
{
    /**
     * Run the demo seeder.
     */
    public function run(): void
    {
        // 1. Users (Admin & Scorer)
        $admin = User::firstOrCreate(
            ['email' => 'admin@kiu.test'],
            [
                'name' => 'Sports Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $scorer = User::firstOrCreate(
            ['email' => 'scorer@kiu.test'],
            [
                'name' => 'Official Scorer',
                'password' => Hash::make('password'),
                'role' => 'scorer',
                'email_verified_at' => now(),
            ]
        );

        // 2. Departments
        $deptData = [
            'Computer Science',
            'Business Administration',
            'Mathematics',
            'Physics',
            'Earth Sciences',
            'Biological Sciences',
            'Chemistry',
            'Educational Development',
        ];

        $depts = [];
        foreach ($deptData as $name) {
            $depts[$name] = Department::firstOrCreate(['name' => $name]);
        }

        // 3. Venues
        $venueData = [
            'KIU Main Sports Ground',
            'University Football Stadium',
            'Indoor Gymnasium & Arena',
            'Basketball & Volleyball Courts',
        ];

        $venues = [];
        foreach ($venueData as $v) {
            $venues[$v] = Venue::firstOrCreate(['name' => $v]);
        }

        // 4. Sports Disciplines
        $sportsData = [
            ['name' => 'Cricket', 'category' => 'boys', 'rules' => 'T20 format, ICC tournament rules, white ball.'],
            ['name' => 'Football', 'category' => 'boys', 'rules' => '90-minute regulation, FIFA university laws, 11-a-side.'],
            ['name' => 'Volleyball', 'category' => 'boys', 'rules' => 'Best of 5 sets, 25-point rally scoring system.'],
            ['name' => 'Badminton', 'category' => 'girls', 'rules' => 'Singles & Doubles, BWF 21-point rally.'],
            ['name' => 'Table Tennis', 'category' => 'boys', 'rules' => 'ITTF best of 5 sets, 11 points.'],
            ['name' => 'Basketball', 'category' => 'boys', 'rules' => 'Four 10-minute quarters, FIBA standard court.'],
        ];

        $sports = [];
        foreach ($sportsData as $sd) {
            $sports[$sd['name'] . '_' . $sd['category']] = Sport::firstOrCreate(
                ['name' => $sd['name'], 'category' => $sd['category']],
                ['rules' => $sd['rules']]
            );
        }

        // -------------------------------------------------------------
        // 5. PAST COMPLETED EDITION: KIU Gala 2025
        // -------------------------------------------------------------
        $edition2025 = Edition::firstOrCreate(
            ['year' => 2025],
            [
                'title' => 'KIU Annual Sports Gala 2025',
                'status' => 'completed',
                'start_date' => Carbon::create(2025, 10, 10),
                'end_date' => Carbon::create(2025, 10, 18),
                'description' => 'The Silver Jubilee Sports Gala celebrating 25 years of educational and athletic prominence in Gilgit-Baltistan with 14 competing academic departments.',
            ]
        );

        // 2025 Champions & Winners (Gold, Silver, Bronze)
        $winners2025 = [
            [
                'sport' => $sports['Cricket_boys'],
                'champ' => $depts['Computer Science'],
                'runner' => $depts['Business Administration'],
                'bronze' => $depts['Mathematics'],
                'score' => '174/6 (20 ov) vs 148/9 (20 ov)',
            ],
            [
                'sport' => $sports['Football_boys'],
                'champ' => $depts['Physics'],
                'runner' => $depts['Computer Science'],
                'bronze' => $depts['Biological Sciences'],
                'score' => '2 - 1',
            ],
            [
                'sport' => $sports['Volleyball_boys'],
                'champ' => $depts['Mathematics'],
                'runner' => $depts['Earth Sciences'],
                'bronze' => $depts['Educational Development'],
                'score' => '3 - 2 sets (15-13 final)',
            ],
            [
                'sport' => $sports['Badminton_girls'],
                'champ' => $depts['Biological Sciences'],
                'runner' => $depts['Chemistry'],
                'bronze' => $depts['Computer Science'],
                'score' => '21-18, 19-21, 21-16',
            ],
            [
                'sport' => $sports['Table Tennis_boys'],
                'champ' => $depts['Computer Science'],
                'runner' => $depts['Mathematics'],
                'bronze' => $depts['Business Administration'],
                'score' => '3 - 1 sets',
            ],
        ];

        foreach ($winners2025 as $w) {
            Winner::updateOrCreate(
                ['edition_id' => $edition2025->id, 'sport_id' => $w['sport']->id],
                [
                    'winner_department_id' => $w['champ']->id,
                    'runner_up_department_id' => $w['runner']->id,
                    'second_runner_up_department_id' => $w['bronze']->id ?? null,
                    'final_score' => $w['score'],
                ]
            );
        }

        // -------------------------------------------------------------
        // 6. RUNNING ACTIVE EDITION: KIU Gala 2026
        // -------------------------------------------------------------
        $edition2026 = Edition::firstOrCreate(
            ['year' => 2026],
            [
                'title' => 'KIU Annual Sports Gala 2026',
                'status' => 'running',
                'start_date' => Carbon::today()->subDays(3),
                'end_date' => Carbon::today()->addDays(6),
                'description' => 'The current official edition of the KIU Sports Gala featuring live match scoring, inter-departmental points standings, and campus athletics.',
            ]
        );

        // Register 2026 Teams across departments
        $teams2026 = [];

        // Cricket Teams
        $teams2026['cricket_cs'] = Team::firstOrCreate(
            ['edition_id' => $edition2026->id, 'sport_id' => $sports['Cricket_boys']->id, 'department_id' => $depts['Computer Science']->id],
            ['name' => 'CS Thunderbolts', 'status' => 'approved']
        );
        $teams2026['cricket_bba'] = Team::firstOrCreate(
            ['edition_id' => $edition2026->id, 'sport_id' => $sports['Cricket_boys']->id, 'department_id' => $depts['Business Administration']->id],
            ['name' => 'BBA Gladiators', 'status' => 'approved']
        );
        $teams2026['cricket_phy'] = Team::firstOrCreate(
            ['edition_id' => $edition2026->id, 'sport_id' => $sports['Cricket_boys']->id, 'department_id' => $depts['Physics']->id],
            ['name' => 'Physics Quantum XI', 'status' => 'approved']
        );
        $teams2026['cricket_earth'] = Team::firstOrCreate(
            ['edition_id' => $edition2026->id, 'sport_id' => $sports['Cricket_boys']->id, 'department_id' => $depts['Earth Sciences']->id],
            ['name' => 'Earth Rangers', 'status' => 'approved']
        );

        // Football Teams
        $teams2026['football_cs'] = Team::firstOrCreate(
            ['edition_id' => $edition2026->id, 'sport_id' => $sports['Football_boys']->id, 'department_id' => $depts['Computer Science']->id],
            ['name' => 'CS Strikers FC', 'status' => 'approved']
        );
        $teams2026['football_math'] = Team::firstOrCreate(
            ['edition_id' => $edition2026->id, 'sport_id' => $sports['Football_boys']->id, 'department_id' => $depts['Mathematics']->id],
            ['name' => 'Matrix Math FC', 'status' => 'approved']
        );
        $teams2026['football_bio'] = Team::firstOrCreate(
            ['edition_id' => $edition2026->id, 'sport_id' => $sports['Football_boys']->id, 'department_id' => $depts['Biological Sciences']->id],
            ['name' => 'Bio Hawks FC', 'status' => 'approved']
        );

        // Volleyball Teams
        $teams2026['volleyball_cs'] = Team::firstOrCreate(
            ['edition_id' => $edition2026->id, 'sport_id' => $sports['Volleyball_boys']->id, 'department_id' => $depts['Computer Science']->id],
            ['name' => 'CS Smashers', 'status' => 'approved']
        );
        $teams2026['volleyball_math'] = Team::firstOrCreate(
            ['edition_id' => $edition2026->id, 'sport_id' => $sports['Volleyball_boys']->id, 'department_id' => $depts['Mathematics']->id],
            ['name' => 'Math Spikers', 'status' => 'approved']
        );
        $teams2026['volleyball_phy'] = Team::firstOrCreate(
            ['edition_id' => $edition2026->id, 'sport_id' => $sports['Volleyball_boys']->id, 'department_id' => $depts['Physics']->id],
            ['name' => 'Physics Blockers', 'status' => 'approved']
        );
        $teams2026['volleyball_chem'] = Team::firstOrCreate(
            ['edition_id' => $edition2026->id, 'sport_id' => $sports['Volleyball_boys']->id, 'department_id' => $depts['Chemistry']->id],
            ['name' => 'Chemistry Aces', 'status' => 'approved']
        );

        // Populate sample players for teams
        $playersMap = [
            'cricket_cs' => [
                ['name' => 'Kamran Ali', 'reg_no' => 'KIU-2023-CS-001', 'contact' => '0345-1234567'],
                ['name' => 'Sohail Abbas', 'reg_no' => 'KIU-2023-CS-014', 'contact' => '0345-1234568'],
                ['name' => 'Daniyal Baig', 'reg_no' => 'KIU-2024-CS-022', 'contact' => '0345-1234569'],
            ],
            'cricket_bba' => [
                ['name' => 'Hamza Tariq', 'reg_no' => 'KIU-2023-BBA-012', 'contact' => '0346-2345671'],
                ['name' => 'Waqas Ahmed', 'reg_no' => 'KIU-2023-BBA-033', 'contact' => '0346-2345672'],
            ],
            'football_cs' => [
                ['name' => 'Zeeshan Haider', 'reg_no' => 'KIU-2022-CS-101', 'contact' => '0312-9876543'],
                ['name' => 'Bilal Hassan', 'reg_no' => 'KIU-2022-CS-102', 'contact' => '0312-9876544'],
            ],
            'football_math' => [
                ['name' => 'Rizwan Shah', 'reg_no' => 'KIU-2023-MTH-005', 'contact' => '0333-5551234'],
                ['name' => 'Adeel Khan', 'reg_no' => 'KIU-2023-MTH-018', 'contact' => '0333-5551235'],
            ],
        ];

        foreach ($playersMap as $teamKey => $players) {
            if (isset($teams2026[$teamKey])) {
                foreach ($players as $pData) {
                    Player::firstOrCreate(
                        ['team_id' => $teams2026[$teamKey]->id, 'name' => $pData['name']],
                        ['reg_no' => $pData['reg_no'], 'contact' => $pData['contact']]
                    );
                }
            }
        }

        // -------------------------------------------------------------
        // 7. FIXTURES & RESULTS FOR RUNNING EDITION 2026
        // -------------------------------------------------------------

        // Completed Match 1 (Yesterday) - Cricket: CS vs BBA
        $f1 = Fixture::firstOrCreate(
            [
                'edition_id' => $edition2026->id,
                'sport_id' => $sports['Cricket_boys']->id,
                'team1_id' => $teams2026['cricket_cs']->id,
                'team2_id' => $teams2026['cricket_bba']->id,
            ],
            [
                'venue_id' => $venues['KIU Main Sports Ground']->id,
                'match_date' => Carbon::yesterday()->toDateString(),
                'match_time' => '10:00:00',
                'round' => 'Group Match 1',
                'status' => 'completed',
            ]
        );

        $kamranAli = Player::where('name', 'Kamran Ali')->first();
        Result::updateOrCreate(
            ['fixture_id' => $f1->id],
            [
                'team1_score' => 165,
                'team2_score' => 140,
                'winner_team_id' => $teams2026['cricket_cs']->id,
                'player_of_the_match_id' => $kamranAli?->id,
                'summary' => 'CS Thunderbolts won by 25 runs after a commanding half-century by Kamran Ali.',
            ]
        );

        // Completed Match 2 (Yesterday) - Football: CS vs Math
        $f2 = Fixture::firstOrCreate(
            [
                'edition_id' => $edition2026->id,
                'sport_id' => $sports['Football_boys']->id,
                'team1_id' => $teams2026['football_cs']->id,
                'team2_id' => $teams2026['football_math']->id,
            ],
            [
                'venue_id' => $venues['University Football Stadium']->id,
                'match_date' => Carbon::yesterday()->toDateString(),
                'match_time' => '14:30:00',
                'round' => 'Group Match',
                'status' => 'completed',
            ]
        );

        $zeeshanHaider = Player::where('name', 'Zeeshan Haider')->first();
        Result::updateOrCreate(
            ['fixture_id' => $f2->id],
            [
                'team1_score' => 3,
                'team2_score' => 1,
                'winner_team_id' => $teams2026['football_cs']->id,
                'player_of_the_match_id' => $zeeshanHaider?->id,
                'summary' => 'Dominant midfield control led CS to a 3-1 victory in the opening group fixture.',
            ]
        );

        // TODAY Match 1 (Live) - Cricket: Physics vs Earth Sciences
        Fixture::firstOrCreate(
            [
                'edition_id' => $edition2026->id,
                'sport_id' => $sports['Cricket_boys']->id,
                'team1_id' => $teams2026['cricket_phy']->id,
                'team2_id' => $teams2026['cricket_earth']->id,
            ],
            [
                'venue_id' => $venues['KIU Main Sports Ground']->id,
                'match_date' => Carbon::today()->toDateString(),
                'match_time' => '10:00:00',
                'round' => 'Group Match 2',
                'status' => 'live',
            ]
        );

        // TODAY Match 2 (Scheduled) - Football: Math vs Bio
        Fixture::firstOrCreate(
            [
                'edition_id' => $edition2026->id,
                'sport_id' => $sports['Football_boys']->id,
                'team1_id' => $teams2026['football_math']->id,
                'team2_id' => $teams2026['football_bio']->id,
            ],
            [
                'venue_id' => $venues['University Football Stadium']->id,
                'match_date' => Carbon::today()->toDateString(),
                'match_time' => '15:30:00',
                'round' => 'Group Match 2',
                'status' => 'scheduled',
            ]
        );

        // Volleyball Knockout Matches (Semi Finals & Final)
        $vSemi1 = Fixture::firstOrCreate(
            [
                'edition_id' => $edition2026->id,
                'sport_id' => $sports['Volleyball_boys']->id,
                'team1_id' => $teams2026['volleyball_cs']->id,
                'team2_id' => $teams2026['volleyball_math']->id,
            ],
            [
                'venue_id' => $venues['Basketball & Volleyball Courts']->id,
                'match_date' => Carbon::yesterday()->toDateString(),
                'match_time' => '11:00:00',
                'round' => 'Semi Final 1',
                'status' => 'completed',
            ]
        );

        Result::updateOrCreate(
            ['fixture_id' => $vSemi1->id],
            [
                'team1_score' => 3,
                'team2_score' => 1,
                'winner_team_id' => $teams2026['volleyball_cs']->id,
                'summary' => 'CS Smashers dominated the final set 25-18 to advance to the Grand Final.',
            ]
        );

        $vSemi2 = Fixture::firstOrCreate(
            [
                'edition_id' => $edition2026->id,
                'sport_id' => $sports['Volleyball_boys']->id,
                'team1_id' => $teams2026['volleyball_phy']->id,
                'team2_id' => $teams2026['volleyball_chem']->id,
            ],
            [
                'venue_id' => $venues['Basketball & Volleyball Courts']->id,
                'match_date' => Carbon::yesterday()->toDateString(),
                'match_time' => '13:00:00',
                'round' => 'Semi Final 2',
                'status' => 'completed',
            ]
        );

        Result::updateOrCreate(
            ['fixture_id' => $vSemi2->id],
            [
                'team1_score' => 3,
                'team2_score' => 2,
                'winner_team_id' => $teams2026['volleyball_phy']->id,
                'summary' => 'Thrilling 5-set nailbiter won 15-13 in the deciding set by Physics.',
            ]
        );

        // Volleyball Grand Championship Final (Today)
        $vFinal = Fixture::firstOrCreate(
            [
                'edition_id' => $edition2026->id,
                'sport_id' => $sports['Volleyball_boys']->id,
                'team1_id' => $teams2026['volleyball_cs']->id,
                'team2_id' => $teams2026['volleyball_phy']->id,
            ],
            [
                'venue_id' => $venues['Basketball & Volleyball Courts']->id,
                'match_date' => Carbon::today()->toDateString(),
                'match_time' => '16:00:00',
                'round' => 'Final',
                'status' => 'completed',
            ]
        );

        Result::updateOrCreate(
            ['fixture_id' => $vFinal->id],
            [
                'team1_score' => 3,
                'team2_score' => 2,
                'winner_team_id' => $teams2026['volleyball_cs']->id,
                'summary' => 'CS Smashers crowned Volleyball Champions 2026 in an electric atmosphere!',
            ]
        );

        // -------------------------------------------------------------
        // 8. NEWS BULLETINS
        // -------------------------------------------------------------
        News::firstOrCreate(
            ['title' => 'KIU Annual Sports Gala 2026 Officially Inaugurated'],
            [
                'edition_id' => $edition2026->id,
                'body' => "The Karakoram International University Annual Sports Gala 2026 opened today in a vibrant celebration of athletics and sportsmanship.\n\nThe Vice Chancellor formally declared the tournament open amidst an inspiring parade of departmental contingents at the KIU Main Sports Ground. Over 800 student-athletes from 12 faculties will compete across six athletic disciplines throughout the 10-day tournament.\n\nAll students, faculty, and local sports enthusiasts are warmly invited to attend the games.",
                'published_at' => Carbon::today()->subDays(2),
            ]
        );

        News::firstOrCreate(
            ['title' => 'Cricket & Football Fixtures Schedule Revised for Day 3'],
            [
                'edition_id' => $edition2026->id,
                'body' => "The Directorate of Sports has published the updated schedule for tomorrow's key fixtures.\n\nMatch kickoffs will begin promptly at 10:00 AM. Ground coordinators and faculty team managers are advised to ensure player lineups are submitted 30 minutes before official coin tosses.\n\nLive scores and standings are updated continuously on the official web portal.",
                'published_at' => Carbon::today()->subDay(),
            ]
        );

        News::firstOrCreate(
            ['title' => 'University Establishes Fair Play Committee and Medical Station'],
            [
                'edition_id' => $edition2026->id,
                'body' => "To uphold university discipline and ensure athlete safety, a dedicated Medical Support Desk and Fair Play Scrutiny Committee have been stationed at the Indoor Gymnasium and Main Ground.\n\nQualified paramedical staff will be on duty during all competitive matches.",
                'published_at' => Carbon::today(),
            ]
        );
    }
}

