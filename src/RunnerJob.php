<?php
    namespace Hdgarau\Runners;

    class RunnerJob
    {
        protected array $_dependencies = [];
        public function __construct(protected iRunner $_runner)
        {
            $this->_dependencies = $this->_runner->dependencies();
        }
        public function className( ) : string
        {
            return get_class( $this->_runner );
        }
        public function check( array $runnedJobs ) : bool
        {
            return $this->readyToRun( $runnedJobs );
        }
        public function readyToRun( )
        {
            return count( $this->_dependencies) == 0;
        }
        protected function _removeDependecies( array $runnedJobs )
        {
            $this->_dependencies = array_diff($this->_dependencies, $runnedJobs);
        }
        public function run( string $className, array $params = [],bool $store = true ) : bool
        {
            return RunnerHandler::run( $className, $params, $store );
        }
    }