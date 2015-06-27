<?
//Set 0.0.3
class Set{
    private $elements;  //Array of elements
    private $pointer;   //Current element pointer
    public function __construct($elements=null){
        if(is_array($elements)){
            $this->elements=$elements;
        }
        elseif($elements!=""){
            $this->elements=array($elements);
        }
        else{
            $this->elements=array();
        }
        $this->pointer=0;
    }
    //Add the elements
    public function add($elements){
        if(is_array($elements)){
            foreach($elements as $v){
                $this->push($v);
            }
        }
        else{
            $this->push($elements);
        }
    }
    //Chek element for exists
    public function exists($element){
        foreach($this->elements as $v){
            if($v==$element) return true;
        }
        return false;
    }
    //Push an element
    public function push($element){
        if($this->exists($element)) return false;
        array_push($this->elements, $element);
        return true;
    }
    //Get elements count
    public function count(){
        return count($this->elements);
    }
    //Read current element
    public function read(){
        if(isset($this->elements[$this->pointer])) return $this->elements[$this->pointer++];
        return false;
    }
    //Set pointer
    public function setPointer($i){
        $this->pointer=$i;
    }
    public function getPointer(){
        return $this->pointer;
    }
    //Get elements
    public function get(){
        return $this->elements;
    }
}
?>