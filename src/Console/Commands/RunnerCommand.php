<?php

namespace Hdgarau\Runners\Console\Commands;

use Hdgarau\Runners\RunnerHandler;
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
        foreach( $paths as $path )
        {
            $allFiles = \File::files($path);
            foreach($allFiles as $file)
            {
                $this->_run($file);
            }
        }
        $allFiles = \File::files(MakeRunnerCommand::getPathDestiny(true) );
        foreach($allFiles as $file)
        {
            $this->_run($file,true);
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
    protected function _parseClassName(string $file)
    {
        $filestr = file_get_contents($file);
        preg_match('/\bnamespace\b.+?(\w+\\\\?)+/i' ,$filestr, $matches);
        $namespace = trim(substr($matches[0],10));
        preg_match('/\bclass\b.+?(\w+)/i' ,$filestr, $matches);
        $className = $matches[1];
        return $namespace . '\\' . $className;
    }
}
