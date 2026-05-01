<?php

namespace App\Console\Commands;

use App\Actions\SitemapAction;
use Illuminate\Console\Command;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'generate:sitemap';

    protected $description = 'Generate sitemap';

    public function handle(): void
    {
        try {
            $this->output->title('Generating Sitemap...');

            // Create an instance of SitemapController and invoke the sitemap generation
            $sitemapController = app(SitemapAction::class);
            $sitemapController();

            $this->info('Sitemap generated successfully!');
        } catch (\Exception $exception) {
            $this->error('Something went wrong generating sitemap!');
            $this->error($exception->getMessage());
        }
    }
}
