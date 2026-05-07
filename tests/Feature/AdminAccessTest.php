<?php

use App\Livewire\Admin\Categories\Index as CategoryIndex;
use App\Livewire\Admin\Categories\Trash as CategoryTrash;
use App\Livewire\Admin\Discounts\Index as DiscountIndex;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('non admin users cannot access admin area', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $response = $this->actingAs($user)->get(route('admin.products.index'));

    $response->assertForbidden();
});

test('admins can access dashboard', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertSuccessful();
});

test('admin can create coupon from discounts page', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    Livewire::actingAs($admin)
        ->test(DiscountIndex::class)
        ->set('code', 'NEW15')
        ->set('name', 'New User 15')
        ->set('discount_percentage', 15)
        ->call('save');

    expect(Coupon::query()->where('code', 'NEW15')->exists())->toBeTrue();
});

test('admin can trash and restore categories', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $category = Category::factory()->create();

    Livewire::actingAs($admin)
        ->test(CategoryIndex::class)
        ->call('trash', $category->id);

    expect($category->fresh()->trashed())->toBeTrue();

    Livewire::actingAs($admin)
        ->test(CategoryTrash::class)
        ->call('restore', $category->id);

    expect($category->fresh()->trashed())->toBeFalse();
});

test('admin can permanently delete trashed category', function () {
    $admin = User::factory()->create(['is_admin' => true]);
    $category = Category::factory()->create();
    $category->delete();

    Livewire::actingAs($admin)
        ->test(CategoryTrash::class)
        ->call('destroy', $category->id);

    expect(Category::withTrashed()->find($category->id))->toBeNull();
});
