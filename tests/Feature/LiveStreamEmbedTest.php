<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LiveStreamEmbedTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $scorer;
    private Edition $edition;
    private Sport $sport;
    private Venue $venue;
    private Team $team1;
    private Team $team2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->scorer = User::factory()->create([
            'role' => 'scorer',
        ]);

        $this->edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-10',
            'status' => 'running',
        ]);

        $this->sport = Sport::create([
            'name' => 'Cricket Men',
            'category' => 'boys',
            'scoring_type' => 'runs',
        ]);

        $this->venue = Venue::create([
            'name' => 'Cricket Ground 1',
        ]);

        $dept1 = Department::create(['name' => 'Computer Science', 'code' => 'CS']);
        $dept2 = Department::create(['name' => 'Management Sciences', 'code' => 'MS']);

        $this->team1 = Team::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
            'department_id' => $dept1->id,
            'name' => 'CS Strikers',
        ]);

        $this->team2 = Team::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
            'department_id' => $dept2->id,
            'name' => 'MS Kings',
        ]);
    }

    public function test_fixture_model_detects_platforms_and_generates_embed_urls(): void
    {
        // 1. YouTube watch URL
        $f1 = new Fixture(['stream_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ']);
        $this->assertEquals('youtube', $f1->stream_platform);
        $this->assertEquals('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0&modestbranding=1', $f1->embed_stream_url);

        // 2. YouTube short URL (youtu.be)
        $f2 = new Fixture(['stream_url' => 'https://youtu.be/abcdefghijk']);
        $this->assertEquals('youtube', $f2->stream_platform);
        $this->assertEquals('https://www.youtube-nocookie.com/embed/abcdefghijk?rel=0&modestbranding=1', $f2->embed_stream_url);

        // 3. YouTube live URL
        $f3 = new Fixture(['stream_url' => 'https://www.youtube.com/live/12345678901']);
        $this->assertEquals('youtube', $f3->stream_platform);
        $this->assertEquals('https://www.youtube-nocookie.com/embed/12345678901?rel=0&modestbranding=1', $f3->embed_stream_url);

        // 4. Facebook URL
        $fbUrl = 'https://www.facebook.com/kiusports/videos/987654321/';
        $f4 = new Fixture(['stream_url' => $fbUrl]);
        $this->assertEquals('facebook', $f4->stream_platform);
        $this->assertStringContainsString('https://www.facebook.com/plugins/video.php?href=', $f4->embed_stream_url);

        // 5. Empty stream
        $f5 = new Fixture(['stream_url' => null]);
        $this->assertNull($f5->stream_platform);
        $this->assertNull($f5->embed_stream_url);
    }

    public function test_admin_can_create_fixture_with_stream_url(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.fixtures.store'), [
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
            'team1_id' => $this->team1->id,
            'team2_id' => $this->team2->id,
            'venue_id' => $this->venue->id,
            'match_date' => '2026-11-05',
            'match_time' => '10:00',
            'round' => 'Semi Final',
            'status' => 'scheduled',
            'stream_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response->assertRedirect(route('admin.fixtures.index'));

        $this->assertDatabaseHas('fixtures', [
            'edition_id' => $this->edition->id,
            'stream_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'round' => 'Semi Final',
        ]);
    }

    public function test_scorer_can_update_stream_url_from_score_entry(): void
    {
        $fixture = Fixture::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
            'team1_id' => $this->team1->id,
            'team2_id' => $this->team2->id,
            'status' => 'live',
        ]);

        $response = $this->actingAs($this->scorer)->post(route('admin.score-entry.store'), [
            'fixture_id' => $fixture->id,
            'team1_score' => 45,
            'team2_score' => 20,
            'status' => 'live',
            'stream_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $fixture->refresh();
        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $fixture->stream_url);
    }

    public function test_public_match_detail_renders_video_iframe_when_stream_url_is_present(): void
    {
        $fixture = Fixture::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
            'team1_id' => $this->team1->id,
            'team2_id' => $this->team2->id,
            'venue_id' => $this->venue->id,
            'status' => 'live',
            'stream_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response = $this->get(route('matches.show', $fixture));

        $response->assertStatus(200);
        $response->assertSee('Official Video Broadcast');
        $response->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0&modestbranding=1');
        $response->assertSee('LIVE STREAM');
        $response->assertSee('Open Video');
    }

    public function test_public_match_detail_hides_stream_section_when_no_stream(): void
    {
        $fixture = Fixture::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
            'team1_id' => $this->team1->id,
            'team2_id' => $this->team2->id,
            'venue_id' => $this->venue->id,
            'status' => 'live',
            'stream_url' => null,
        ]);

        $response = $this->get(route('matches.show', $fixture));

        $response->assertStatus(200);
        $response->assertSee('live-stream-section');
        $response->assertSee('hidden');
    }

    public function test_live_api_returns_stream_url_and_embed_url(): void
    {
        $fixture = Fixture::create([
            'edition_id' => $this->edition->id,
            'sport_id' => $this->sport->id,
            'team1_id' => $this->team1->id,
            'team2_id' => $this->team2->id,
            'venue_id' => $this->venue->id,
            'status' => 'live',
            'stream_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response = $this->getJson(route('api.live-scores.show', $fixture));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $fixture->id,
            'stream_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'embed_stream_url' => 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?rel=0&modestbranding=1',
            'stream_platform' => 'youtube',
        ]);
    }
}
