<?php

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\Edition;
use App\Models\Fixture;
use App\Models\MatchPhoto;
use App\Models\Sport;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class MatchPhotoTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $scorer;
    private Fixture $fixture;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->admin = User::factory()->create([
            'email' => 'admin@kiu.test',
            'role' => 'admin',
        ]);

        $this->scorer = User::factory()->create([
            'email' => 'scorer@kiu.test',
            'role' => 'scorer',
        ]);

        $edition = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);
        $sport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'Computer Science']);
        $dept2 = Department::create(['name' => 'Business Admin']);

        $team1 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept1->id, 'name' => 'CS XI']);
        $team2 = Team::create(['edition_id' => $edition->id, 'sport_id' => $sport->id, 'department_id' => $dept2->id, 'name' => 'BBA XI']);

        $this->fixture = Fixture::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'team1_id' => $team1->id,
            'team2_id' => $team2->id,
            'status' => 'scheduled',
        ]);
    }

    public function test_guest_is_redirected_from_upload_and_delete(): void
    {
        $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

        $this->post(route('admin.fixtures.photos.store', $this->fixture), [
            'photos' => [$file],
        ])->assertRedirect(route('login'));

        $photo = MatchPhoto::create([
            'fixture_id' => $this->fixture->id,
            'image' => 'matches/sample.jpg',
            'uploaded_by' => $this->admin->id,
        ]);

        $this->delete(route('admin.fixtures.photos.destroy', $photo))
            ->assertRedirect(route('login'));
    }

    public function test_admin_can_upload_multiple_photos_and_resize_to_max_1200px(): void
    {
        // 1600x900 image (wider than 1200px)
        $file1 = UploadedFile::fake()->image('wide_action.jpg', 1600, 900);
        // 800x600 image (already smaller than 1200px)
        $file2 = UploadedFile::fake()->image('standard.jpg', 800, 600);

        $response = $this->actingAs($this->admin)->post(route('admin.fixtures.photos.store', $this->fixture), [
            'photos' => [$file1, $file2],
            'caption' => 'Opening Ceremony',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('match_photos', 2);

        $photos = MatchPhoto::where('fixture_id', $this->fixture->id)->get();
        $this->assertCount(2, $photos);

        $manager = new ImageManager(new Driver());

        foreach ($photos as $photo) {
            $this->assertEquals($this->admin->id, $photo->uploaded_by);
            $this->assertEquals('Opening Ceremony', $photo->caption);
            $this->assertTrue(Storage::disk('public')->exists($photo->image));

            // Verify image was resized to <= 1200px width
            $savedFileFullPath = Storage::disk('public')->path($photo->image);
            $img = $manager->read($savedFileFullPath);
            $this->assertLessThanOrEqual(1200, $img->width());
        }
    }

    public function test_scorer_can_upload_match_photos(): void
    {
        $file = UploadedFile::fake()->image('scorer_shot.jpg', 1400, 700);

        $response = $this->actingAs($this->scorer)->post(route('admin.fixtures.photos.store', $this->fixture), [
            'photos' => [$file],
            'caption' => 'Goal Highlight',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('match_photos', [
            'fixture_id' => $this->fixture->id,
            'caption' => 'Goal Highlight',
            'uploaded_by' => $this->scorer->id,
        ]);
    }

    public function test_admin_and_scorer_can_delete_match_photo(): void
    {
        $file = UploadedFile::fake()->image('to_delete.jpg', 600, 400);

        $this->actingAs($this->admin)->post(route('admin.fixtures.photos.store', $this->fixture), [
            'photos' => [$file],
        ]);

        $photo = MatchPhoto::first();
        $this->assertNotNull($photo);
        $this->assertTrue(Storage::disk('public')->exists($photo->image));

        // Delete photo as scorer
        $delResponse = $this->actingAs($this->scorer)->delete(route('admin.fixtures.photos.destroy', $photo));
        $delResponse->assertRedirect();

        $this->assertDatabaseMissing('match_photos', ['id' => $photo->id]);
        $this->assertFalse(Storage::disk('public')->exists($photo->image));
    }
}
