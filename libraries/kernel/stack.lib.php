<?
//Stack 0.0.1
class Stack{
    private $stack;     //Array of elements
    public function __construct(){
        $this->stack=array();
    }
    public function push($element){
        array_push($this->stack, $element);
    }
    public function pop(){
        return array_pop($this->stack);
    }
}
?>