<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\MatchVideo;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchVideoReelTest extends TestCase
{
    use RefreshDatabase;

    private function createFixture(): Fixture
    {
        $edition = Edition::create([
            'year' => 2026,
            'title' => 'KIU Gala 2026',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(5),
            'status' => 'running',
        ]);

        $sport = Sport::create([
            'name' => 'Football',
            'category' => 'boys',
            'scoring_type' => 'goals',
        ]);

        $dept1 = Department::create(['name' => 'CS Dept']);
        $dept2 = Department::create(['name' => 'BBA Dept']);

        $team1 = Team::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'department_id' => $dept1->id,
            'name' => 'CS Tigers',
        ]);

        $team2 = Team::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'department_id' => $dept2->id,
            'name' => 'BBA Lions',
        ]);

        return Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'match_date' => Carbon::today(),
            'match_time' => '15:00',
            'status' => 'live',
            'round' => 'Quarter Final',
        ]);
    }

    public function test_admin_can_add_and_delete_match_video_reel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $fixture = $this->createFixture();

        $response = $this->actingAs($admin)
            ->post(route('admin.fixtures.videos.store', $fixture), [
                'title' => 'Winning Penalty Shootout Decider',
                'video_url' => 'https://www.youtube.com/shorts/5wW9-N1nQoE',
                'duration' => '0:45',
                'type' => 'reel',
                'caption' => 'Dramatic 5th penalty kick save',
            ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('match_videos', [
            'fixture_id' => $fixture->id,
            'title' => 'Winning Penalty Shootout Decider',
            'type' => 'reel',
            'duration' => '0:45',
        ]);

        $video = MatchVideo::where('fixture_id', $fixture->id)->first();
        $this->assertNotNull($video);
        $this->assertStringContainsString('youtube-nocookie.com/embed/5wW9-N1nQoE', $video->embed_url);

        // Delete video
        $deleteResponse = $this->actingAs($admin)
            ->delete(route('admin.fixtures.videos.destroy', $video));

        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('match_videos', ['id' => $video->id]);
    }

    public function test_scorer_can_add_video_clip_but_guest_cannot(): void
    {
        $scorer = User::factory()->create(['role' => 'scorer']);
        $fixture = $this->createFixture();

        // Guest attempt
        $guestResponse = $this->post(route('admin.fixtures.videos.store', $fixture), [
            'title' => 'Guest attempt',
            'video_url' => 'https://example.com/clip.mp4',
        ]);
        $guestResponse->assertRedirect(route('login'));

        // Scorer attempt
        $scorerResponse = $this->actingAs($scorer)
            ->post(route('admin.fixtures.videos.store', $fixture), [
                'title' => 'Asim Boundary Six',
                'video_url' => 'https://example.com/videos/boundary-six.mp4',
                'duration' => '0:30',
                'type' => 'highlight',
            ]);

        $scorerResponse->assertSessionHas('success');
        $this->assertDatabaseHas('match_videos', [
            'fixture_id' => $fixture->id,
            'title' => 'Asim Boundary Six',
        ]);
    }

    public function test_match_detail_page_displays_video_reels_and_viewer(): void
    {
        $fixture = $this->createFixture();

        MatchVideo::create([
            'fixture_id' => $fixture->id,
            'title' => 'Stunning Header Goal',
            'video_url' => 'https://www.youtube.com/shorts/5wW9-N1nQoE',
            'duration' => '0:40',
            'type' => 'reel',
            'caption' => 'Incredible aerial coordination',
        ]);

        $response = $this->get(route('matches.show', $fixture));

        $response->assertOk();
        $response->assertSee('Match Video Highlights & Short Reels', false);
        $response->assertSee('Stunning Header Goal');
        $response->assertSee('reel-viewer-modal');
        $response->assertSee('openReelViewer(0)', false);
    }

    public function test_gallery_page_renders_video_reels(): void
    {
        $fixture = $this->createFixture();

        MatchVideo::create([
            'fixture_id' => $fixture->id,
            'title' => 'Championship Trophy Ceremony Moment',
            'video_url' => 'https://www.youtube.com/watch?v=5wW9-N1nQoE',
            'duration' => '1:15',
            'type' => 'ceremony',
            'caption' => 'Auditorium cup presentation',
        ]);

        $response = $this->get(route('gallery'));

        $response->assertOk();
        $response->assertSee('Championship Trophy Ceremony Moment');
        $response->assertSee('REEL');
    }
}
