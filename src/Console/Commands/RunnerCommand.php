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
        $path = $this->argument('path') ?? MakeRunnerCommand::getPathDestiny();
        $allFiles = \File::allFiles($path);
        foreach($allFiles as $file)
        {
            $this->_run($file);
        }
        return self::SUCCESS;
    }
    protected function _run($file)
    {
        require_once $file;
        $className = $this->_parseClassName($file);
        if(RunnerHandler::once($className))
        {
            $this->info('Runned ................ ' . $className);
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
