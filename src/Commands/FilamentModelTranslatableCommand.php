<?php

namespace KaanTanis\FilamentModelTranslatable\Commands;

use Illuminate\Console\Command;

class FilamentModelTranslatableCommand extends Command
{
    public $signature = 'filament-model-translatable';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
