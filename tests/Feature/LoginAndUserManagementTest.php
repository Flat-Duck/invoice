<?php

namespace Tests\Feature;

use App\Livewire\Users\Index;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoginAndUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_hidden_super_admin_can_login_and_is_not_stored(): void
    {
        $this->post('/login', ['username' => 'superadmin', 'password' => 'superadmin'])
            ->assertRedirect('/');

        $this->assertTrue(session('invoice_super_admin'));
        $this->assertSame('Super Admin', session('invoice_user_name'));
        $this->assertDatabaseMissing('users', ['username' => 'superadmin']);
        $this->get('/')->assertOk()->assertSee('Users')->assertSee('/users', false);
        $this->get('/users')->assertOk()->assertSee('Manage access to this application.');
    }

    public function test_active_database_user_can_login_but_inactive_user_cannot(): void
    {
        User::factory()->create([
            'username' => 'operator',
            'password' => 'secret12',
            'active' => true,
        ]);

        $this->post('/login', ['username' => 'operator', 'password' => 'secret12'])
            ->assertRedirect('/');

        auth()->logout();
        User::where('username', 'operator')->update(['active' => false]);

        $this->post('/login', ['username' => 'operator', 'password' => 'secret12'])
            ->assertSessionHasErrors('username');
    }

    public function test_user_cannot_delete_self(): void
    {
        $user = User::factory()->create(['username' => 'owner', 'active' => true]);

        Livewire::actingAs($user)
            ->test(Index::class)
            ->call('delete', $user)
            ->assertHasErrors('delete');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_superadmin_username_is_reserved_in_user_crud(): void
    {
        Livewire::test(Index::class)
            ->set('name', 'Fake Admin')
            ->set('username', 'superadmin')
            ->set('password', 'secret12')
            ->call('save')
            ->assertHasErrors('username');
    }
}
