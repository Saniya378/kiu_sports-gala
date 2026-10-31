<?php

namespace Tests\Feature\Admin;

use App\Models\Edition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EditionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/editions');
        $response->assertRedirect('/login');
    }

    public function test_scorer_is_forbidden_from_admin_editions(): void
    {
        $scorer = User::factory()->create(['role' => 'scorer']);

        $response = $this->actingAs($scorer)->get('/admin/editions');
        $response->assertStatus(403);
    }

    public function test_admin_can_view_editions_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->get('/admin/editions');
        $response->assertStatus(200);
    }

    public function test_admin_can_create_edition_with_banner(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $banner = UploadedFile::fake()->create('gala_banner.jpg', 100, 'image/jpeg');

        $response = $this->actingAs($admin)->post('/admin/editions', [
            'title' => 'KIU Gala 2026',
            'year' => 2026,
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-07',
            'status' => 'upcoming',
            'banner' => $banner,
            'description' => 'Annual sports festival.',
        ]);

        $response->assertRedirect(route('admin.editions.index'));
        $this->assertDatabaseHas('editions', [
            'title' => 'KIU Gala 2026',
            'year' => 2026,
            'status' => 'upcoming',
        ]);

        $edition = Edition::where('year', 2026)->first();
        $this->assertNotNull($edition->banner);
        Storage::disk('public')->assertExists($edition->banner);
    }

    public function test_admin_can_update_edition(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $edition = Edition::create([
            'title' => 'Initial Title',
            'year' => 2025,
            'status' => 'upcoming',
        ]);

        $response = $this->actingAs($admin)->put("/admin/editions/{$edition->id}", [
            'title' => 'Updated Title',
            'year' => 2025,
            'status' => 'running',
        ]);

        $response->assertRedirect(route('admin.editions.index'));
        $this->assertDatabaseHas('editions', [
            'id' => $edition->id,
            'title' => 'Updated Title',
            'status' => 'running',
        ]);
    }

    public function test_admin_can_delete_edition(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $edition = Edition::create([
            'title' => 'To Be Deleted',
            'year' => 2024,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/editions/{$edition->id}");

        $response->assertRedirect(route('admin.editions.index'));
        $this->assertDatabaseMissing('editions', [
            'id' => $edition->id,
        ]);
    }
}
