<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Clean extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clean';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will clean both filenames and ID3-tags from matching files in the music folder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        foreach(['clean-filenames', 'clean-id3-tags'] as $command) {
            $this->call($command);
            $this->separate();
        }
        $this->info('Cleaning complete!');
    }

    /**
     * Display separator.
     */
    private function separate() {
        $this->line(str_repeat('-', 9));
    }
}
