<?php

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('product description renders markdown as HTML on the storefront', function () {
    $product = Product::factory()->create([
        'description' => "Line one.\n\n**Bold** and *italic*.",
    ]);

    $html = (string) $product->renderedDescriptionHtml();

    expect($html)->toContain('<strong>Bold</strong>')
        ->and($html)->toContain('<em>italic</em>');
});

test('product description passes through when it starts with an HTML tag', function () {
    $product = Product::factory()->create([
        'description' => '<p class="intro">Trusted HTML from admin.</p>',
    ]);

    expect((string) $product->renderedDescriptionHtml())
        ->toBe('<p class="intro">Trusted HTML from admin.</p>');
});

test('product descriptionPlainExcerpt strips markup', function () {
    $product = Product::factory()->create([
        'description' => 'Intro with **emphasis** and more text after that.',
    ]);

    $excerpt = $product->descriptionPlainExcerpt(24);

    expect($excerpt)->not->toContain('*')
        ->and($excerpt)->toContain('Intro');
});
