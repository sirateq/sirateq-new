<?php

use Illuminate\Support\Facades\Artisan;

test('users:create artisan command is registered', function () {
    expect(Artisan::all())->toHaveKey('users:create');
});

test('users:reset-password artisan command is registered', function () {
    expect(Artisan::all())->toHaveKey('users:reset-password');
});
