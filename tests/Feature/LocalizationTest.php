<?php

namespace Tests\Feature;

use App\Models\Edition;
use App\Models\Sport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_switch_locale_to_urdu_and_see_rtl_and_translations(): void
    {
        Edition::create(['year' => 2026, 'title' => 'KIU Gala 2026', 'status' => 'running']);
        Sport::create(['name' => 'Cricket', 'category' => 'boys']);

        // Switch to Urdu
        $response = $this->get(route('locale.switch', 'ur'));
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'ur');

        // Access home page with Urdu session
        $pageResponse = $this->withSession(['locale' => 'ur'])->get(route('home'));
        $pageResponse->assertOk();
        $pageResponse->assertSee('dir="rtl"', false);
        $pageResponse->assertSee('آج کے مقابلے');
        $pageResponse->assertSee('نتائج');
        $pageResponse->assertSee('شیڈول');
    }

    public function test_user_can_switch_back_to_english(): void
    {
        Edition::create(['year' => 2026, 'title' => 'KIU Gala 2026', 'status' => 'running']);

        // Switch to English
        $response = $this->get(route('locale.switch', 'en'));
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'en');

        // Access home page with English session
        $pageResponse = $this->withSession(['locale' => 'en'])->get(route('home'));
        $pageResponse->assertOk();
        $pageResponse->assertSee('dir="ltr"', false);
        $pageResponse->assertSee("Today&#039;s Matches", false);
    }
}

