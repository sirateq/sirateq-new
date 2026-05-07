<?php

use App\Livewire\Admin\Customers\Index as CustomersIndex;
use App\Livewire\Admin\Customers\Show as CustomersShow;
use App\Livewire\Admin\Users\Index as AdminUsersIndex;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('non admin cannot manage admin users or customers', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    $this->actingAs($user)->get(route('admin.customers.index'))->assertForbidden();
});

test('admin can create another admin user', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(AdminUsersIndex::class)
        ->set('name', 'Ops Lead')
        ->set('email', 'ops@example.com')
        ->set('password', 'newpassword123')
        ->set('password_confirmation', 'newpassword123')
        ->call('saveAdmin');

    $created = User::query()->where('email', 'ops@example.com')->first();
    expect($created)->not->toBeNull();
    expect($created->is_admin)->toBeTrue();
});

test('admin cannot revoke their own admin access', function () {
    $admin = User::factory()->admin()->create();

    Livewire::actingAs($admin)
        ->test(AdminUsersIndex::class)
        ->call('revokeAdmin', $admin->id);

    expect($admin->fresh()->is_admin)->toBeTrue();
});

test('admin can revoke another admin when more than one admin exists', function () {
    $first = User::factory()->admin()->create(['email' => 'first@example.com']);
    $second = User::factory()->admin()->create(['email' => 'second@example.com']);

    Livewire::actingAs($second)
        ->test(AdminUsersIndex::class)
        ->call('revokeAdmin', $first->id);

    expect($first->fresh()->is_admin)->toBeFalse();
    expect($second->fresh()->is_admin)->toBeTrue();
});

test('admin cannot open customer profile for an admin user', function () {
    $admin = User::factory()->admin()->create();
    $target = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('admin.customers.show', $target))->assertNotFound();
});

test('admin can view customer list and profile with orders', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create(['is_admin' => false, 'name' => 'Pat Customer']);
    $order = Order::factory()->create(['user_id' => $customer->id, 'order_number' => '700001']);

    Livewire::actingAs($admin)
        ->test(CustomersIndex::class)
        ->assertSee('Pat Customer');

    Livewire::actingAs($admin)
        ->test(CustomersShow::class, ['user' => $customer])
        ->assertSee('700001');
});
