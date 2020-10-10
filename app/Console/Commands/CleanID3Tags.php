<?php

namespace App\Console\Commands;

use App\MusicCleaner;
use Illuminate\Console\Command;

/**
 * Class CleanID3Tags
 * @package App\Console\Commands
 */
class CleanID3Tags extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean-id3-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean junk from ID3 tags.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mc = new MusicCleaner;
        $extensions = $mc->fileExtensions();
        $files = $mc->listFiles($extensions);
        $files->each(function ($file) {
            #dd(exec(sprintf('ffprobe "%s"', $file)));
            #dd(exec(sprintf('ffprobe "%s"', $file)));
        });
    }
}
