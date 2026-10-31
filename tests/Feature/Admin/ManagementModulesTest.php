<?php

namespace Tests\Feature\Admin;

use App\Models\Department;
use App\Models\Sport;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagementModulesTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $scorer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->scorer = User::factory()->create(['role' => 'scorer']);
    }

    public function test_departments_crud_and_unique_validation(): void
    {
        // Scorer forbidden
        $this->actingAs($this->scorer)
            ->get(route('admin.departments.index'))
            ->assertStatus(403);

        // Admin can view index
        $this->actingAs($this->admin)
            ->get(route('admin.departments.index'))
            ->assertStatus(200);

        // Admin can create department
        $response = $this->actingAs($this->admin)
            ->post(route('admin.departments.store'), [
                'name' => 'Computer Science',
            ]);
        $response->assertRedirect(route('admin.departments.index'));
        $this->assertDatabaseHas('departments', ['name' => 'Computer Science']);

        // Duplicate name fails
        $responseDuplicate = $this->actingAs($this->admin)
            ->post(route('admin.departments.store'), [
                'name' => 'Computer Science',
            ]);
        $responseDuplicate->assertSessionHasErrors('name');

        // Admin can update department
        $department = Department::where('name', 'Computer Science')->first();
        $this->actingAs($this->admin)
            ->put(route('admin.departments.update', $department), [
                'name' => 'Dept of Computer Science',
            ])
            ->assertRedirect(route('admin.departments.index'));
        $this->assertDatabaseHas('departments', ['name' => 'Dept of Computer Science']);

        // Admin can delete department
        $this->actingAs($this->admin)
            ->delete(route('admin.departments.destroy', $department))
            ->assertRedirect(route('admin.departments.index'));
        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    public function test_venues_crud_and_unique_validation(): void
    {
        // Scorer forbidden
        $this->actingAs($this->scorer)
            ->get(route('admin.venues.index'))
            ->assertStatus(403);

        // Admin can view index
        $this->actingAs($this->admin)
            ->get(route('admin.venues.index'))
            ->assertStatus(200);

        // Admin can create venue
        $response = $this->actingAs($this->admin)
            ->post(route('admin.venues.store'), [
                'name' => 'Main Stadium Ground',
            ]);
        $response->assertRedirect(route('admin.venues.index'));
        $this->assertDatabaseHas('venues', ['name' => 'Main Stadium Ground']);

        // Duplicate name fails
        $responseDuplicate = $this->actingAs($this->admin)
            ->post(route('admin.venues.store'), [
                'name' => 'Main Stadium Ground',
            ]);
        $responseDuplicate->assertSessionHasErrors('name');

        // Admin can update venue
        $venue = Venue::where('name', 'Main Stadium Ground')->first();
        $this->actingAs($this->admin)
            ->put(route('admin.venues.update', $venue), [
                'name' => 'University Stadium',
            ])
            ->assertRedirect(route('admin.venues.index'));
        $this->assertDatabaseHas('venues', ['name' => 'University Stadium']);

        // Admin can delete venue
        $this->actingAs($this->admin)
            ->delete(route('admin.venues.destroy', $venue))
            ->assertRedirect(route('admin.venues.index'));
        $this->assertDatabaseMissing('venues', ['id' => $venue->id]);
    }

    public function test_sports_crud_and_compound_unique_validation(): void
    {
        // Scorer forbidden
        $this->actingAs($this->scorer)
            ->get(route('admin.sports.index'))
            ->assertStatus(403);

        // Admin can view index
        $this->actingAs($this->admin)
            ->get(route('admin.sports.index'))
            ->assertStatus(200);

        // Admin can create football for boys
        $this->actingAs($this->admin)
            ->post(route('admin.sports.store'), [
                'name' => 'Football',
                'category' => 'boys',
                'rules' => '11 players aside, 45 min halves.',
            ])
            ->assertRedirect(route('admin.sports.index'));
        $this->assertDatabaseHas('sports', [
            'name' => 'Football',
            'category' => 'boys',
        ]);

        // Same sport name for girls is ALLOWED (unique on name + category)
        $this->actingAs($this->admin)
            ->post(route('admin.sports.store'), [
                'name' => 'Football',
                'category' => 'girls',
                'rules' => '11 players aside.',
            ])
            ->assertRedirect(route('admin.sports.index'));
        $this->assertDatabaseHas('sports', [
            'name' => 'Football',
            'category' => 'girls',
        ]);

        // Duplicate sport with same name and same category FAILS
        $responseDuplicate = $this->actingAs($this->admin)
            ->post(route('admin.sports.store'), [
                'name' => 'Football',
                'category' => 'boys',
            ]);
        $responseDuplicate->assertSessionHasErrors('name');

        // Admin can update sport
        $sport = Sport::where('name', 'Football')->where('category', 'boys')->first();
        $this->actingAs($this->admin)
            ->put(route('admin.sports.update', $sport), [
                'name' => 'Soccer',
                'category' => 'boys',
                'rules' => 'Standard FIFA rules apply.',
            ])
            ->assertRedirect(route('admin.sports.index'));
        $this->assertDatabaseHas('sports', [
            'id' => $sport->id,
            'name' => 'Soccer',
            'category' => 'boys',
        ]);

        // Admin can delete sport
        $this->actingAs($this->admin)
            ->delete(route('admin.sports.destroy', $sport))
            ->assertRedirect(route('admin.sports.index'));
        $this->assertDatabaseMissing('sports', ['id' => $sport->id]);
    }
}
