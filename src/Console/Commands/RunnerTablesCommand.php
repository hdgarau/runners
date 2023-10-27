<?php

namespace Hdgarau\Runners\Console\Commands;

use Illuminate\Console\Command;

class RunnerTablesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'runner-handler:tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea los migrations de los runners';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        \File::copyDirectory(__DIR__ . '/../../../migrations/', database_path('migrations/'));
        return self::SUCCESS;
    }
}
