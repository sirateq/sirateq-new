<?php

namespace App\Actions;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapAction
{
    public function __invoke(): void
    {
        $sitemap = Sitemap::create();

        $pages = [
            'home',
            'about-us',
            'services',
            'our-works',
            'products',
            'contact-us',
        ];

        foreach ($pages as $routeName) {
            $sitemap->add(
                Url::create(route($routeName))
                    ->setPriority($routeName === 'home' ? 1.0 : 0.8)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
            );
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));
    }
}
