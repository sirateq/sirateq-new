<?php

use App\Services\ProductImageWatermarkService;

test('watermark subtly changes a jpeg when GD and the bundled font are available', function () {
    if (! extension_loaded('gd') || ! function_exists('imagettftext')) {
        expect(true)->toBeTrue();

        return;
    }

    $font = resource_path('fonts/Inter-Latin.ttf');
    if (! is_readable($font)) {
        expect(true)->toBeTrue();

        return;
    }

    $path = storage_path('app/watermark-test-'.uniqid('', true).'.jpg');
    $im = imagecreatetruecolor(180, 120);
    $bg = imagecolorallocate($im, 90, 90, 95);
    imagefill($im, 0, 0, $bg);
    imagejpeg($im, $path, 95);
    imagedestroy($im);

    $before = hash_file('sha256', $path);

    config(['product_images.watermark.enabled' => true]);
    config(['product_images.watermark.font_path' => $font]);
    config(['product_images.watermark.opacity' => 0.35]);

    ProductImageWatermarkService::applyIfEnabled($path);

    expect(hash_file('sha256', $path))->not->toBe($before);

    @unlink($path);
});

test('watermark is skipped when disabled', function () {
    if (! extension_loaded('gd') || ! function_exists('imagettftext')) {
        expect(true)->toBeTrue();

        return;
    }

    $font = resource_path('fonts/Inter-Latin.ttf');
    if (! is_readable($font)) {
        expect(true)->toBeTrue();

        return;
    }

    $path = storage_path('app/watermark-skip-'.uniqid('', true).'.jpg');
    $im = imagecreatetruecolor(60, 40);
    $bg = imagecolorallocate($im, 40, 40, 40);
    imagefill($im, 0, 0, $bg);
    imagejpeg($im, $path, 95);
    imagedestroy($im);

    $before = hash_file('sha256', $path);

    config(['product_images.watermark.enabled' => false]);
    config(['product_images.watermark.font_path' => $font]);

    ProductImageWatermarkService::applyIfEnabled($path);

    expect(hash_file('sha256', $path))->toBe($before);

    @unlink($path);
});
