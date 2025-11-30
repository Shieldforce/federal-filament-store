<?php

namespace Shieldforce\FederalFilamentStore\Commands;

use Illuminate\Console\Command;

class FederalFilamentStoreCommand extends Command
{
    public $signature = 'federal-filament-store';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
