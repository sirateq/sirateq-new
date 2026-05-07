<?php

use App\Mail\ContactAdminAlert;
use App\Mail\ContactUserConfirmation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

test('contact form accepts post on contact-us', function () {
    Mail::fake();

    $payload = [
        'first_name' => 'Ada',
        'last_name' => 'Lovelace',
        'email' => 'ada@example.com',
        'phone' => '+233000000000',
        'company' => 'Test Co',
        'service' => 'Web Development & Design',
        'message' => 'Hello from the test suite.',
    ];

    $response = $this->post(route('contact-us.submit'), $payload);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    Mail::assertSent(ContactAdminAlert::class);
    Mail::assertSent(ContactUserConfirmation::class, fn (ContactUserConfirmation $mail): bool => $mail->hasTo('ada@example.com'));
});

test('legacy post contact still works', function () {
    Mail::fake();

    $this->post(route('contact.submit'), [
        'first_name' => 'Bob',
        'last_name' => 'Test',
        'email' => 'bob@example.com',
        'phone' => '0200000000',
        'service' => 'Cloud Services',
        'message' => 'Legacy path.',
    ])->assertRedirect()->assertSessionHas('success');

    Mail::assertSent(ContactAdminAlert::class);
});
