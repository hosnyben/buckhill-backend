<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class ReseedDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reseed-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to reseed the database with fresh data. Only for development purposes.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if( config('app.env') == 'production' ) {
            $this->error('This command can only be run in testing/development environment.');
            return;
        }

        Artisan::call('migrate:refresh');
        // Remove files from storage, disk "files", except .gitignore and sample.png
        $excludedFiles = ['.gitignore', 'sample.png'];
        $files = array_filter(Storage::disk('files')->allFiles(), function ($file) use ($excludedFiles) {
            return !in_array($file, $excludedFiles);
        });
        Storage::disk('files')->delete($files);

        Artisan::call('db:seed');

        $this->info('Database reseeded successfully.');
    }
}
