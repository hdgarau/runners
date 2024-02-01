<?php
    namespace Hdgarau\Runners;

    class RunnerJob
    {
        const IGNORED = -1;
        const ERROR = -2;
        const WAITING = 1;
        const CHECKED = 2;
        const RUNNED = 3;


        protected string $_lastMsg = '';
        protected ?int $_status;
        protected array $_dependencies = [];

        public function __construct(protected iRunner $_runner, protected bool $_always )
        {
            $this->_dependencies = $this->_runner->dependencies();
        }
        public function className( ) : string
        {
            return get_class( $this->_runner );
        }
        public function lastMsg( ) : string
        {
            return $this->_lastMsg;
        }
        public function status( ) : ?int
        {
            return $this->_status;
        }
        public function check( array $runnedJobs, array $runnersToRun, array $runnersAll ) : bool
        {
            $this->_status = self::WAITING;
            $this->_removeDependecies($runnedJobs);
            if( ! $this->checkDependencies( ))
            {
                $this->_lastMsg = $this->className() . ' has ' . count( $this->_dependencies) .  
                    ' dependencies still.';
                return false;
            }
            if( ! $this->checkCondition( $runnedJobs, $runnersToRun, $runnersAll ))
            {
                $this->_lastMsg = $this->className() . ' condition return false still';
                return false;
            }
            $this->_status = self::CHECKED;
            $this->_lastMsg = $this->className() . ' was checked succefull.';
            return true;
        }
        
        public function checkDependencies( ) : bool
        {
            return count( $this->_dependencies) == 0;
        }
        public function checkCondition( $runned, $toRun, $all) : bool
        {
            $canRun = $this->_runner->canRun();
            return match(true)
            {
                is_callable($canRun) => $canRun($runned, $toRun, $all),
                default => $canRun 
            };
        }
        
        protected function _removeDependecies( array $runnedJobs )
        {
            $this->_dependencies = array_diff($this->_dependencies, $runnedJobs);
        }
        public function run(  ) : bool
        {
            if(!$this->_always && RunnerHandler::count($this->className()) > 0)
            {
                $this->_lastMsg = $this->className() . ' .... Ignored';
                $this->_status = self::IGNORED;
                return true;
            }
            $method = $this->_always ? 'run' : 'once';
            if( ! RunnerHandler::$method( $this->_runner, $this->_always ))
            {
                $this->_status = self::ERROR;
                $this->_lastMsg = $this->className() . ' .... ERROR ON RUN';
                return false;
            }
            $this->_status = self::RUNNED;
            $this->_lastMsg = $this->className() . ' .... Runned';
            return true;


        }
    }