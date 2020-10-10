<?php

namespace App\Console\Commands;

use App\MusicCleaner;
use Illuminate\Console\Command;
use Illuminate\Support\Str;


class CleanFilenames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean-filenames';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean a string from filename in music folder.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mc = new MusicCleaner;
        $cleanedFiles = $mc->cleanFilenames();
        if ( $cleanedFiles->isEmpty() ) {
            $this->error(sprintf('No files matched %s %s.', Str::plural('string', count($mc->stringsToClean())), '"' . implode('", "', $mc->stringsToClean()) . '"'));
            return;
        }
        $this->info(sprintf('Cleaned %s %s from %s files:', Str::plural('string', count($mc->stringsToClean())), '"' . implode('", "', $mc->stringsToClean()) . '"', $cleanedFiles->count() ));
        $cleanedFiles->each(function ($file) {
            $this->info($file);
        });
    }
}
