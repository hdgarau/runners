<?php
    namespace Hdgarau\Runners;

    abstract class RunnerHandler
    {
        private static iRunnerModel $_model;

        static public function once( string $className, array $params = [] ) : bool
        {
            return static::times($className,1);
        }
        static public function run( string $className, array $params = [] ) : bool
        {
            if(static::_classNameToRunner($className, $params )->handler())
            {
                static::_add($className);
                return true;
            }
            return false;
        }
        static public function times( string $className, int $times, array $params = [] ) : bool
        {
            if( static::count($className) < $times)
            {
                return static::run( $className, $params );
            }
            return false;
        }
        static public function count ( ?string $className = null ) : int
        {
            return static::$_model->count($className) ?? 0;
        }

        static public function setModel( iRunnerModel $model )
        {
            static::$_model = $model;
        }
        static public function deleteByDatatimeRange( \Datetime $from ,\Datetime $since ) : int
        {
            return static::$_model->deleteByDatatimeRange($from, $since);
        }
        static public function deleteByClassName( string $className ) : int
        {
            return static::$_model->deleteByClassName( $className );
        }
        static public function clear(  ) : bool
        {
            return static::$_model->clear( );
        }
        //private methods
        static private function _classNameToRunner( string $className, array $params = [] ) : iRunner
        {
            return new $className(...$params);
        }
        static private function _add( string $className ) : bool
        {
            return static::$_model->add( $className);
        }
    }