<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_officials_management(): void
    {
        $response = $this->get(route('admin.users.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_scorer_is_forbidden_from_officials_management(): void
    {
        $scorer = User::factory()->create(['role' => 'scorer']);

        $response = $this->actingAs($scorer)->get(route('admin.users.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_officials_list_and_filter_by_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $scorer = User::factory()->create(['role' => 'scorer', 'name' => 'Ground Scorer 1']);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));
        $response->assertOk();
        $response->assertSee('Officials &amp; Scorers', false);
        $response->assertSee('Ground Scorer 1');

        $filterResponse = $this->actingAs($admin)->get(route('admin.users.index', ['role' => 'scorer']));
        $filterResponse->assertOk();
        $filterResponse->assertSee('Ground Scorer 1');
    }

    public function test_admin_can_create_a_new_scorer(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Cricket Ground Scorer',
            'email' => 'cricket.scorer@kiu.edu.pk',
            'role' => 'scorer',
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Cricket Ground Scorer',
            'email' => 'cricket.scorer@kiu.edu.pk',
            'role' => 'scorer',
        ]);

        $created = User::where('email', 'cricket.scorer@kiu.edu.pk')->first();
        $this->assertTrue(Hash::check('secret1234', $created->password));
    }

    public function test_admin_can_create_a_new_department_coordinator(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $dept = \App\Models\Department::create(['name' => 'Computer Science']);

        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Dr. Coordinator',
            'email' => 'cs.coord@kiu.edu.pk',
            'role' => 'coordinator',
            'department_id' => $dept->id,
            'password' => 'secret1234',
            'password_confirmation' => 'secret1234',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Dr. Coordinator',
            'email' => 'cs.coord@kiu.edu.pk',
            'role' => 'coordinator',
            'department_id' => $dept->id,
        ]);
    }

    public function test_admin_can_update_official_and_reset_password(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $scorer = User::factory()->create([
            'role' => 'scorer',
            'email' => 'old.scorer@kiu.edu.pk',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $scorer), [
            'name' => 'Updated Scorer Name',
            'email' => 'new.scorer@kiu.edu.pk',
            'role' => 'scorer',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $scorer->id,
            'name' => 'Updated Scorer Name',
            'email' => 'new.scorer@kiu.edu.pk',
        ]);

        $scorer->refresh();
        $this->assertTrue(Hash::check('newpassword123', $scorer->password));
    }

    public function test_admin_cannot_demote_their_own_account_or_delete_themselves(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Demote attempt
        $demoteResponse = $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'scorer',
        ]);
        $demoteResponse->assertSessionHasErrors('role');
        $this->assertEquals('admin', $admin->fresh()->role);

        // Delete self attempt
        $deleteResponse = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));
        $deleteResponse->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_can_delete_other_officials(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $scorer = User::factory()->create(['role' => 'scorer']);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $scorer));
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $scorer->id]);
    }
}

