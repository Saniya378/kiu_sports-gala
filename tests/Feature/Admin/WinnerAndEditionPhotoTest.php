<?php

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\Edition;
use App\Models\EditionPhoto;
use App\Models\Sport;
use App\Models\User;
use App\Models\Winner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class WinnerAndEditionPhotoTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $scorer;

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
    }

    public function test_scorer_is_forbidden_from_winners_and_edition_photos(): void
    {
        $edition = Edition::create(['title' => 'Gala 2024', 'year' => 2024]);
        $sport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);

        // Winners index
        $this->actingAs($this->scorer)->get(route('admin.winners.index'))->assertForbidden();

        // Photo upload
        $file = UploadedFile::fake()->image('test.jpg', 600, 400);
        $this->actingAs($this->scorer)
            ->post(route('admin.editions.photos.store', $edition), ['photos' => [$file]])
            ->assertForbidden();
    }

    public function test_admin_can_create_update_and_delete_winner(): void
    {
        $edition = Edition::create(['title' => 'Gala 2024', 'year' => 2024]);
        $sport = Sport::create(['name' => 'Cricket', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'Computer Science']);
        $dept2 = Department::create(['name' => 'Management Sciences']);

        // Create
        $response = $this->actingAs($this->admin)->post(route('admin.winners.store'), [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'winner_department_id' => $dept1->id,
            'runner_up_department_id' => $dept2->id,
            'final_score' => '175/4 vs 160/9 (Won by 15 runs)',
        ]);

        $response->assertRedirect(route('admin.winners.index'));
        $this->assertDatabaseHas('winners', [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'winner_department_id' => $dept1->id,
            'runner_up_department_id' => $dept2->id,
            'final_score' => '175/4 vs 160/9 (Won by 15 runs)',
        ]);

        $winner = Winner::first();

        // Index
        $indexRes = $this->actingAs($this->admin)->get(route('admin.winners.index'));
        $indexRes->assertOk();
        $indexRes->assertSee('Computer Science');
        $indexRes->assertSee('Management Sciences');

        // Edit page
        $editRes = $this->actingAs($this->admin)->get(route('admin.winners.edit', $winner));
        $editRes->assertOk();
        $editRes->assertSee('Edit Winner');

        // Update
        $updateRes = $this->actingAs($this->admin)->put(route('admin.winners.update', $winner), [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'winner_department_id' => $dept2->id,
            'runner_up_department_id' => $dept1->id,
            'final_score' => '180/2 vs 179/8',
        ]);

        $updateRes->assertRedirect(route('admin.winners.index'));
        $this->assertDatabaseHas('winners', [
            'id' => $winner->id,
            'winner_department_id' => $dept2->id,
            'final_score' => '180/2 vs 179/8',
        ]);

        // Delete
        $delRes = $this->actingAs($this->admin)->delete(route('admin.winners.destroy', $winner));
        $delRes->assertRedirect(route('admin.winners.index'));
        $this->assertDatabaseMissing('winners', ['id' => $winner->id]);
    }

    public function test_enforces_unique_edition_and_sport_on_winners(): void
    {
        $edition = Edition::create(['title' => 'Gala 2024', 'year' => 2024]);
        $sport = Sport::create(['name' => 'Volleyball', 'category' => 'boys']);
        $dept1 = Department::create(['name' => 'CS']);
        $dept2 = Department::create(['name' => 'BBA']);

        Winner::create([
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'winner_department_id' => $dept1->id,
        ]);

        // Try creating duplicate for same edition and sport
        $response = $this->actingAs($this->admin)->post(route('admin.winners.store'), [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'winner_department_id' => $dept2->id,
        ]);

        $response->assertSessionHasErrors(['sport_id']);
        $this->assertDatabaseCount('winners', 1);
    }

    public function test_winner_and_runner_up_cannot_be_same_department(): void
    {
        $edition = Edition::create(['title' => 'Gala 2024', 'year' => 2024]);
        $sport = Sport::create(['name' => 'Basketball', 'category' => 'boys']);
        $dept = Department::create(['name' => 'CS']);

        $response = $this->actingAs($this->admin)->post(route('admin.winners.store'), [
            'edition_id' => $edition->id,
            'sport_id' => $sport->id,
            'winner_department_id' => $dept->id,
            'runner_up_department_id' => $dept->id,
        ]);

        $response->assertSessionHasErrors(['runner_up_department_id']);
        $this->assertDatabaseCount('winners', 0);
    }

    public function test_admin_can_multi_upload_edition_photos_and_resize_to_1200px(): void
    {
        $edition = Edition::create(['title' => 'Gala 2023', 'year' => 2023]);
        $sport = Sport::create(['name' => 'Football', 'category' => 'boys']);

        // Fake image larger than 1200px
        $file1 = UploadedFile::fake()->image('gala_parade.jpg', 1800, 1000);
        $file2 = UploadedFile::fake()->image('trophy_moment.jpg', 800, 600);

        $response = $this->actingAs($this->admin)->post(route('admin.editions.photos.store', $edition), [
            'photos' => [$file1, $file2],
            'sport_id' => $sport->id,
            'caption' => 'Memories of 2023 Gala',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseCount('edition_photos', 2);

        $photos = EditionPhoto::where('edition_id', $edition->id)->get();
        $this->assertCount(2, $photos);

        $manager = new ImageManager(new Driver());

        foreach ($photos as $photo) {
            $this->assertEquals($sport->id, $photo->sport_id);
            $this->assertEquals('Memories of 2023 Gala', $photo->caption);
            $this->assertTrue(Storage::disk('public')->exists($photo->image));

            $fullPath = Storage::disk('public')->path($photo->image);
            $img = $manager->read($fullPath);
            $this->assertLessThanOrEqual(1200, $img->width());
        }

        // Test deletion
        $photoToDelete = $photos->first();
        $delResponse = $this->actingAs($this->admin)->delete(route('admin.editions.photos.destroy', $photoToDelete));
        $delResponse->assertRedirect();

        $this->assertDatabaseMissing('edition_photos', ['id' => $photoToDelete->id]);
        $this->assertFalse(Storage::disk('public')->exists($photoToDelete->image));
    }
}

