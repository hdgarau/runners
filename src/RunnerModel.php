<?php
namespace Hdgarau\Runners;

use Illuminate\Database\Eloquent\Model;

class RunnerModel extends Model implements iRunnerModel
{
    protected $table = 'runners';
    public function count( ?string $className = null ) : int
    {
        if(is_null($className))
        {
            return $this->query()->count();
        }
        return $this->where('class_name',$className)->count();
    }
    public function add ( string $className) : bool
    {
        return (bool) $this->insert(['class_name'=>$className]);
    }

    public function deleteByDatatimeRange( \Datetime $from ,\Datetime $since ) : int
    {
        return $this->whereDateBetween('created_at',[$from, $since])->delete();
    }
    public function deleteByClassName( string $className ) : int
    {
        return $this->where('class_name', $className )->delete();
    }
    public function clear(  ) : bool
    {
        $this->truncate( );
        return true;
    }
} 