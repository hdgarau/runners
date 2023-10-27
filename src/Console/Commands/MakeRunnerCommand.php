<?php

namespace Hdgarau\Runners\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;
use Hdgarau\Runners\RunnerHandler;

#[AsCommand(name: 'make:runner')]
class MakeRunnerCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:runner';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new runner class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Runner';
    protected $_prefix;
    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/runner.stub');
    }
    protected function _setPrefix()
    {
        $prefix = RunnerHandler::count();
        $prefix += count(\File::allFiles($this::getPathDestiny()));
        $prefix = str_pad($prefix, 5, '0', STR_PAD_LEFT);
        $this->_prefix = $prefix . '_';
    }

    public function handle()
    {
        //
        $this->_setPrefix();
        return parent::handle();
    }
    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $runner = class_basename(Str::ucfirst(str_replace('Runner', '', $name)));


        $namespace = $this->getNamespace(
            Str::replaceFirst($this->rootNamespace(), 'Database\\Runners\\', $this->qualifyClass($this->getNameInput()))
        );

        $replace = [
            '{{ runnerNamespace }}' => $namespace,
            '{{ runner }}' => $runner,
            '{{runner}}' => $runner,
        ];

        return str_replace(
            array_keys($replace), array_values($replace), parent::buildClass($name)
        );
    }
    static public function getPathDestiny()
    {
        return database_path('/runners/');
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = (string) Str::of($name)->replaceFirst($this->rootNamespace(), '')->finish('Runner');
        
        return $this::getPathDestiny() . $this->_prefix . str_replace('\\', '/', $name).'.php';
    }
}
