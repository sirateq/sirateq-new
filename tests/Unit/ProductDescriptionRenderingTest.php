<?php

use App\Models\Product;

test('renderedDescriptionHtml converts GitHub-flavored Markdown to HTML', function () {
    $product = new Product([
        'description' => "Line one.\n\n**Bold** and *italic*.",
    ]);

    $html = (string) $product->renderedDescriptionHtml();

    expect($html)->toContain('<strong>Bold</strong>')
        ->and($html)->toContain('<em>italic</em>');
});

test('renderedDescriptionHtml passes through when body starts with an HTML tag', function () {
    $product = new Product([
        'description' => '<p class="intro">Trusted HTML from admin.</p>',
    ]);

    expect((string) $product->renderedDescriptionHtml())
        ->toBe('<p class="intro">Trusted HTML from admin.</p>');
});

test('descriptionPlainExcerpt strips markup for list previews', function () {
    $product = new Product([
        'description' => 'Intro with **emphasis** and more text after that.',
    ]);

    $excerpt = $product->descriptionPlainExcerpt(24);

    expect($excerpt)->not->toContain('*')
        ->and($excerpt)->toContain('Intro');
});
