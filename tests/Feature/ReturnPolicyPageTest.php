<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('return policy page is reachable and shows policy content', function (): void {
    $response = $this->get(route('shop.policies.returns'));

    $response->assertSuccessful();
    $response->assertSeeText(__('Return policy'));
    $response->assertSeeText(__('Returns & refunds'));
});
