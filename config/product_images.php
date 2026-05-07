<?php

return [

    'watermark' => [
        'enabled' => env('PRODUCT_IMAGE_WATERMARK_ENABLED', true),

        /** TrueType font with Latin glyphs (bundled under resources/fonts). */
        'font_path' => env('PRODUCT_IMAGE_WATERMARK_FONT', resource_path('fonts/Inter-Latin.ttf')),

        /** Smaller top line (reference-style); leave empty for a single centered line only. */
        'line1' => env('PRODUCT_IMAGE_WATERMARK_LINE1', 'Posted on'),

        'line2' => env('PRODUCT_IMAGE_WATERMARK_LINE2', 'Sirateq Ghana Shop'),

        /**
         * How visible the watermark is (0–1). Lower = more subtle (reference was bold; this stays light).
         */
        'opacity' => (float) env('PRODUCT_IMAGE_WATERMARK_OPACITY', 0.22),

        'color_r' => 248,
        'color_g' => 248,
        'color_b' => 248,

        'line_gap_scale' => 0.38,

        'jpeg_quality' => 90,
        'webp_quality' => 90,
        'png_compression' => 6,
    ],

];
