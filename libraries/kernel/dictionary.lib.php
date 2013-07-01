<?
//Dictionary 0.1.2
class Dictionary{
    private $elements;	//Array of elements
    public function __construct($elements=null){
	if(is_array($elements)){
	    foreach($elements as $key=>$val){
		$this->set($key, $val);
	    }
	}
	else{
	    $this->elements=array();
	}
    }
    //Add elements
    function add($values){
        foreach($values as $key => $val){
            $this->elements[$key]=$val;
        }
    }
    //Add element
    function set($key, $value){
        $this->elements[$key]=$value;
    }
    function __set($key, $value){
	$this->set($key, $value);
    }
    //Get element
    function get($key="*"){
        if($key=="*"){
            return $this->elements;
        }
        else{
            if(isset($this->elements[$key])){
                return $this->elements[$key];
            }
            else{
                return "";
            }
        }
    }
    function __get($key){
	return $this->get($key);
    }
    //Check element for exists
    function exists($key){
        return isset($this->elements[$key])?true:false;
    }
    //Delete element
    function remove($key){
        unset($this->elements[$key]);
    }
    //Union substitutions
    //If perfix is not null elements from second substitution begining with prefix and underline.
    //If priority process value 1 recuret elements be come from firs subtitution, else from second.
    function union($sub, $prefix="", $rewrite=true){
        if($rewrite){
            foreach($sub->get() as $key => $val){
                $this->elements[$prefix.$key]=$val;
            }
        }
        else{
            foreach($sub->get() as $key => $val){
                if(!$this->exists($prefix.$key)){
                    $this->elements[$prefix.$key]=$val;
                }
            }
        }
    }
    //Get count
    function count(){
        return count($this->elements);
    }
};
?>