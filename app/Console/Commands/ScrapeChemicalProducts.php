<?php

namespace App\Console\Commands;

use App\Jobs\ScrapeChemicalJob;
use Illuminate\Console\Command;

class ScrapeChemicalProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:scrape-chemical-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scrapped chemical webiste';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Dispatch the Job to the queue
            ScrapeChemicalJob::dispatch();

            $this->info('Scraping job has been dispatched successfully!');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
