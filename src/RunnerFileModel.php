<?php
namespace Hdgarau\Runners;

use Illuminate\Database\Eloquent\Model;

class RunnerFileModel implements iRunnerModel
{
    protected array $_data = []; 
    public function __construct( protected string $_fileName )
    {
        try {
            $str = file_get_contents($this->_fileName);
            //$str = str_replace("\\","\\\\",$str);
            $data =  json_decode( $str );
        } catch(\Exception $e) {
            $data = [];
        }
        //dd($data);
        $this->_data = array_map(
            fn($el) => (object) [
                'class_name' => $el->class_name, 
                'created_at' => \DateTime::createFromFormat('Y-m-d',$el->created_at)
            ],$data
        );
    }
    public function save() : bool 
    {
        $a  = fopen($this->_fileName,'w');
        fputs($a, json_encode($this->_data, JSON_PRETTY_PRINT | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));
        return true;    
    }
    public function count( ?string $className = null ) : int
    {
        if(is_null($className))
        {
            return count($this->_data);
        }
        return count( $this->filterByClassName($className));
    }
    public function filterByClassName(string $className) : array
    {
        return array_filter( 
            $this->_data,
            fn($item) => $item->class_name == $className
        );
    }
    public function filterByDatatimeRange( \Datetime $from ,\Datetime $since ) : array
    {
        return array_filter( 
            $this->_data,
            fn($item) => $item->created_at >= $from  && $item->created_at <= $since
        );
    }
    public function add ( string $className) : bool
    {
        array_push( 
            $this->_data, 
            (object) [
                'class_name' => $className, 
                'created_at' => (new \DateTime())->format('Y-m-d H:i:s')
            ]);
        $this->save();
        return true;
    }

    public function deleteByDatatimeRange( \Datetime $from ,\Datetime $since ) : int
    {
        return $this->deleteBy([$this,'filterByDatatimeRange'],[$from, $since]);
    }
    public function deleteByClassName( string $className ) : int
    {
        return $this->deleteBy([$this,'filterByClassName'],[$className]);
    }
    public function deleteBy(Callable $fn, array $params = []) : int
    {
        $prev = count($this->_data);
        $this->_data = $fn(...$params);
        $this->save();
        return $prev - count($this->_data);    
    }

    public function allClasses(  ) : array
    {
        return array_unique( array_map(fn($el) => $el->class_name,$this->_data));
    }
    public function clear(  ) : bool
    {
        unlink($this->_fileName );
        return true;
    }
} 