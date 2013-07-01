<?
//Positions 0.2.0
class Positions{
    private $alias;
    private $positions;
    public function __construct($alias=null){
        $this->alias=$alias;
        $this->positions=array();
    }
    public function read(){
        if($this->alias!=null){
            $file='db/positions/'.$this->alias.'.json';
            if(file_exists($file)){
                $this->positions=json_decode(file_get_contents($file), true);
            }
        }
    }
    public function show(){
        foreach($this->positions as $position){
            Design::symbiontInclude($position);
        }
    }
    public function push($position){
        array_push($this->positions, $position);
    }
    public function save(){
        $f=fopen('db/positions/'.$this->alias.'.json', 'w');
        fwrite($f, json_encode($this->positions));
        fclose($f);
    }
    public function get(){
        return $this->positions;
    }
}
?>