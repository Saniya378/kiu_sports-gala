<?php

namespace Tests\Feature\Admin;

use App\Models\Edition;
use App\Models\News;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class NewsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('admin.news.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_scorer_is_forbidden_from_news_admin(): void
    {
        $scorer = User::factory()->create(['role' => 'scorer']);

        $response = $this->actingAs($scorer)->get(route('admin.news.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_news_index(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        News::create([
            'title' => 'Important Gala Announcement',
            'body' => 'Registration deadline has been extended.',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.news.index'));

        $response->assertStatus(200);
        $response->assertSee('Important Gala Announcement');
    }

    public function test_admin_can_create_news_article_with_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $edition = Edition::create(['title' => 'Gala 2026', 'year' => 2026, 'status' => 'running']);

        $image = UploadedFile::fake()->image('news_banner.jpg', 800, 600);

        $response = $this->actingAs($admin)->post(route('admin.news.store'), [
            'title' => 'Football Tournament Kickoff Confirmed',
            'body' => 'All departmental teams are requested to gather at 09:00 AM.',
            'edition_id' => $edition->id,
            'published_at' => now()->format('Y-m-d H:i:s'),
            'image' => $image,
        ]);

        $response->assertRedirect(route('admin.news.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('news', [
            'title' => 'Football Tournament Kickoff Confirmed',
            'edition_id' => $edition->id,
        ]);

        $news = News::first();
        $this->assertNotNull($news->image);
        Storage::disk('public')->assertExists($news->image);
    }

    public function test_admin_can_update_news_article(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $news = News::create([
            'title' => 'Old Title',
            'body' => 'Old body text',
            'published_at' => now(),
        ]);

        $response = $this->actingAs($admin)->put(route('admin.news.update', $news), [
            'title' => 'Updated News Title',
            'body' => 'Updated comprehensive details about the schedule change.',
            'published_at' => now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertRedirect(route('admin.news.index'));
        $this->assertDatabaseHas('news', [
            'id' => $news->id,
            'title' => 'Updated News Title',
        ]);
    }

    public function test_admin_can_delete_news_article_and_its_image(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $imagePath = 'news/delete_test.jpg';
        Storage::disk('public')->put($imagePath, 'fake image content');

        $news = News::create([
            'title' => 'To Be Deleted',
            'body' => 'Temporary notice content.',
            'image' => $imagePath,
            'published_at' => now(),
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.news.destroy', $news));

        $response->assertRedirect(route('admin.news.index'));
        $this->assertDatabaseMissing('news', ['id' => $news->id]);
        Storage::disk('public')->assertMissing($imagePath);
    }
}
