<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use Tests\TestCase;

class PublicLayoutTest extends TestCase
{
    public function test_public_layout_renders_with_all_navbar_items(): void
    {
        $rendered = View::make('layouts.public', [
            'content' => 'Test Content Body',
        ])->render();

        $this->assertStringContainsString('Home', $rendered);
        $this->assertStringContainsString("Today&#039;s Matches", $rendered);
        $this->assertStringContainsString('Schedule', $rendered);
        $this->assertStringContainsString('Results', $rendered);
        $this->assertStringContainsString('Teams', $rendered);
        $this->assertStringContainsString('Archive', $rendered);
        $this->assertStringContainsString('Hall of Fame', $rendered);
        $this->assertStringContainsString('Gallery', $rendered);
        $this->assertStringContainsString('News', $rendered);

        // Branding and color theme
        $this->assertStringContainsString('KIU', $rendered);
        $this->assertStringContainsString('SPORTS', $rendered);
        $this->assertStringContainsString('bg-[#042f20]', $rendered); // Dark green
        $this->assertStringContainsString('text-amber-400', $rendered); // Gold
        $this->assertStringContainsString('border-amber-400', $rendered); // Gold border

        // Mobile responsive elements
        $this->assertStringContainsString('mobile-menu-toggle-btn', $rendered);
        $this->assertStringContainsString('mobile-nav-menu', $rendered);
    }
}

