<?php
    namespace Hdgarau\Runners;

    abstract class RunnerHandler
    {
        private static iRunnerModel $_model;

        static public function once( iRunner $runner ) : bool
        {
            return static::times($runner,1);
        }
        static public function run( iRunner $runner ) : bool
        {
            if($runner->handler( ))
            {
                static::_add(get_class($runner));
                return true;
            }
            return false;
        }
        static public function times( iRunner $runner, int $times ) : bool
        {
            if( static::count(get_class($runner)) < $times)
            {
                return static::run( $runner );
            }
            return false;
        }
        static public function count ( ?string $className = null ) : int
        {
            return static::$_model->count($className) ?? 0;
        }
        static public function all ( ) : array
        {
            return static::$_model->allClasses();
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
        static private function _add( string $className ) : bool
        {
            return static::$_model->add( $className);
        }
        static public function createJobFromPath(string $path, bool $always = false,array $params = [])
        {
            $className = static::_parseClassName($path);
            return new RunnerJob(new $className(...$params), $always);
        }

        static protected function _parseClassName(string $file) :string
        {
            $filestr = file_get_contents($file);
            preg_match('/\bnamespace\b.+?(\w+\\\\?)+/i' ,$filestr, $matches);
            $namespace = trim(substr($matches[0],10));
            preg_match('/\bclass\b.+?(\w+)/i' ,$filestr, $matches);
            $className = $matches[1];
            return $namespace . '\\' . $className;
        }
    }