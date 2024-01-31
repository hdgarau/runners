<?php
    namespace Hdgarau\Runners;

use Hdgarau\Runners\Console\Commands\MakeRunnerCommand;

    class RunnerQueue
    {
        protected array $_runners = [];
        protected array $_runnedJobs = [];
        protected bool $_wasRunned = false;
        protected array $_output = [];

        public function writeLine(string $msg)
        {
            array_push($this->_output, $msg);
        }
        public function clearBuffer( )
        {
            $this->_output = [];
        }
        public function output() : array
        {
            return $this->_output;
        }
        public function loadFromDirectories( array $paths )
        {
            foreach( $paths as $path )
            {
                $allFiles = \File::files($path);
                foreach($allFiles as $file)
                {
                    $this->addByFile($file);
                }
            }
            $allFiles = \File::files(MakeRunnerCommand::getPathDestiny(true) );
            foreach($allFiles as $file)
            {
                $this->addByFile($file,true);
            }
        }
        public function addByFile(string $path, bool $always = false, array $params = [])
        {
            require $path;
            $this->add( RunnerHandler::createJobFromPath( $path, $always, $params ) );
        }
        public function add( RunnerJob $runner )
        {
            array_push( $this->_runners, $runner );
        }
        public function addToRunned( RunnerJob $runner )
        {
            array_push( $this->_runnedJobs, $runner );
        }
        public function run( ) : bool
        {
            $executeSomething = false;
            foreach($this->toRun( ) as $runnerJob)
            {
                if( $runnerJob->check( ) );
                {
                    if( $runnerJob->run( ) )
                    {
                        $executeSomething = true;
                        $this->addToRunned( $runnerJob );
                    }
                }
                $this->writeLine( $runnerJob->lastMsg( ));
            }
            if( ! $this->finish( ) && $executeSomething )
            {
                return $this->run( );
            }
            $this->_wasRunned = true;
            return $this->finish( );
        }
        public function finish( ) : bool
        {
            return count( $this->toRun( ) ) == 0;
        }
        public function toRun( ) : array
        {
            $toRun = [];
            $runnedClassNames = $this->runnedClassNames( );
            foreach( $this->_runners as $runner )
            {
                if( !in_array( $runner->className( ), $runnedClassNames ) )
                {
                    array_push( $toRun, $runner );
                }
            }
            return $toRun;
        }
        public function wasRunned( ) : bool
        {
            return $this->_wasRunned;
        }
        public function runned( ) : array
        {
            return $this->_runnedJobs;
        }
        protected function _className( )
        {
            return fn($job) => $job->className( );
        }
        public function runnersClassNames( )
        {
            return array_map( [ $this, '_className'], $this->_runners);
        }
        public function runnedClassNames()
        {
            return array_map( [ $this, '_className'], $this->runned());
        }
        public function toRunClassNames()
        {
            return array_map( [ $this, '_className'], $this->toRun());
        }
        public function isLocked()
        {
            return $this->wasRunned() && !$this->finish();
        }
    }