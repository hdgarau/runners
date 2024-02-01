<?php

namespace Hdgarau\Runners\Console\Commands;

use Hdgarau\Runners\RunnerHandler;
use Hdgarau\Runners\RunnerQueue;
use Illuminate\Console\Command;

class RunnerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'runner {path?}';

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
        $paths = $this->argument('path') ? 
            explode(',',$this->argument('path')) :
            [ MakeRunnerCommand::getPathDestiny() ];
        $runnerQueue = new RunnerQueue($this);
        $runnerQueue->loadFromDirectories($paths);
        $runnerQueue->run( );
        if($runnerQueue->isLocked())
        {
            $this->warn('There are some runners pending by deathlock.');
        }
        return self::SUCCESS;
    }
    protected function _run($file, $always = false )
    {
        require_once $file;
        $className = $this->_parseClassName($file);
        if($always)
        {
            if(RunnerHandler::run($className,[], false))
            {
                $this->info('Runned (always) ................ ' . $className);
            }
        }
        elseif(RunnerHandler::once($className))
        {
            $this->info('Runned .......................... ' . $className);
        }
    }
}
