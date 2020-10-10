<?php

namespace App\Console\Commands;

use App\MusicCleaner;
use Illuminate\Console\Command;

/**
 * Class FindFileExtesions
 * @package App\Console\Commands
 */
class FindFileExtesions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find-file-ext';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all file extensions.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mc = new MusicCleaner;
        $extensions = $mc->getFileExtensionsFromFiles();
        $this->info(sprintf('Found %s extensions in files:', $extensions->count()));
        $extensions->each(function ($extension) {
            $this->info($extension);
        });
    }
}
