<?php

namespace App\Services;

use GdImage;
use Illuminate\Support\Facades\Log;

final class ProductImageWatermarkService
{
    /**
     * Apply a subtle centered watermark in place. Skips unsupported formats (e.g. GIF) or missing GD/ font.
     */
    public static function applyIfEnabled(string $absolutePath): void
    {
        if (! config('product_images.watermark.enabled', true)) {
            return;
        }

        if (! extension_loaded('gd') || ! function_exists('imagettfbbox') || ! function_exists('imagettftext')) {
            Log::warning('Product image watermark skipped: GD with FreeType is required.');

            return;
        }

        $fontPath = config('product_images.watermark.font_path');
        if (! is_string($fontPath) || $fontPath === '' || ! is_readable($fontPath)) {
            Log::warning('Product image watermark skipped: font file is not readable.', ['path' => $fontPath]);

            return;
        }

        $info = @getimagesize($absolutePath);
        if ($info === false || ! isset($info['mime'])) {
            return;
        }

        $mime = $info['mime'];
        if ($mime === 'image/gif') {
            return;
        }

        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($absolutePath),
            'image/png' => @imagecreatefrompng($absolutePath),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($absolutePath) : false,
            default => false,
        };

        if (! $image instanceof GdImage) {
            return;
        }

        try {
            imagealphablending($image, true);
            if ($mime === 'image/png' || $mime === 'image/webp') {
                imagesavealpha($image, true);
            }

            self::drawWatermark($image, $fontPath, (int) $info[0], (int) $info[1]);

            match ($mime) {
                'image/jpeg' => imagejpeg($image, $absolutePath, (int) config('product_images.watermark.jpeg_quality', 90)),
                'image/png' => self::savePng($image, $absolutePath),
                'image/webp' => function_exists('imagewebp')
                    ? imagewebp($image, $absolutePath, (int) config('product_images.watermark.webp_quality', 90))
                    : null,
                default => null,
            };
        } finally {
            imagedestroy($image);
        }
    }

    private static function savePng(GdImage $image, string $path): void
    {
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagepng($image, $path, (int) config('product_images.watermark.png_compression', 6));
    }

    private static function drawWatermark(GdImage $image, string $fontPath, int $width, int $height): void
    {
        $line1 = trim((string) config('product_images.watermark.line1', ''));
        $line2 = trim((string) config('product_images.watermark.line2', 'Sirateq Ghana Shop'));

        if ($line1 === '' && $line2 === '') {
            return;
        }

        if ($line1 !== '' && $line2 === '') {
            $line2 = $line1;
            $line1 = '';
        }

        $opacity = (float) config('product_images.watermark.opacity', 0.22);
        $opacity = max(0.06, min(0.55, $opacity));
        $alpha = (int) round(127 * (1 - $opacity));

        $r = (int) config('product_images.watermark.color_r', 248);
        $g = (int) config('product_images.watermark.color_g', 248);
        $b = (int) config('product_images.watermark.color_b', 248);
        $color = imagecolorallocatealpha($image, $r, $g, $b, $alpha);
        if ($color === false) {
            return;
        }

        $minSide = min($width, $height);
        $base = max(9.0, $minSide / 24.0);
        $size1 = $line1 !== '' ? $base * 0.58 : 0.0;
        $size2 = $base * 0.82;

        if ($line1 === '') {
            $bbox2 = imagettfbbox($size2, 0.0, $fontPath, $line2);
            if ($bbox2 === false) {
                return;
            }

            $line2Height = $bbox2[1] - $bbox2[7];
            $blockTop = ($height - $line2Height) / 2;
            $baseline2 = $blockTop - $bbox2[7];
            self::drawCenteredLine($image, $size2, $baseline2, $color, $fontPath, $line2, $width);

            return;
        }

        $bbox1 = imagettfbbox($size1, 0.0, $fontPath, $line1);
        $bbox2 = imagettfbbox($size2, 0.0, $fontPath, $line2);
        if ($bbox1 === false || $bbox2 === false) {
            return;
        }

        $line1Height = $bbox1[1] - $bbox1[7];
        $line2Height = $bbox2[1] - $bbox2[7];
        $gap = $base * (float) config('product_images.watermark.line_gap_scale', 0.38);
        $blockTop = ($height - $line1Height - $gap - $line2Height) / 2;

        $baseline1 = $blockTop - $bbox1[7];
        $baseline2 = $baseline1 + $bbox1[1] + $gap - $bbox2[7];

        self::drawCenteredLine($image, $size1, $baseline1, $color, $fontPath, $line1, $width);
        self::drawCenteredLine($image, $size2, $baseline2, $color, $fontPath, $line2, $width);
    }

    private static function drawCenteredLine(
        GdImage $image,
        float $size,
        float $baseline,
        int $color,
        string $fontPath,
        string $text,
        int $width,
    ): void {
        $bbox = imagettfbbox($size, 0.0, $fontPath, $text);
        if ($bbox === false) {
            return;
        }

        $cx = $width / 2.0;
        $textWidth = $bbox[2] - $bbox[0];
        $x = (int) round($cx - $textWidth / 2 - $bbox[0]);
        $y = (int) round($baseline);

        imagettftext($image, $size, 0.0, $x, $y, $color, $fontPath, $text);
    }
}
