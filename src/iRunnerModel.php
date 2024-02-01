<?php
    namespace Hdgarau\Runners;

    interface iRunnerModel
    {
        public function count( ?string $className = null ) : int;
        public function add ( string $className) : bool;
        public function deleteByDatatimeRange( \Datetime $from ,\Datetime $since ) : int;
        public function deleteByClassName( string $className ) : int;
        public function clear(  ) : bool;
        public function allClasses(  ) : array;
    }