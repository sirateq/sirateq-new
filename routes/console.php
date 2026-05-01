<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Generate sitemap hourly (light)
Schedule::command('generate:sitemap')
    ->daily()
    ->withoutOverlapping();
