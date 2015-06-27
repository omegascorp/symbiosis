<?
//Positions 0.1.6
class Positions{
    private $id;
    private $alias;
    private $positions;
    public function __construct($id=null, $alias=null){
        $this->id=$id;
        $this->alias=$alias;
        $this->positions=array();
    }
    public function read(){
        global $db;
        if($this->id!=null||$this->alias!=null){
            $where=array();
            if($this->id!=null){
                $where['id']=$this->id;
            }
            else{
                $where['alias']=$this->alias;
            }
            $positions=$db->select('positions', '*', $where);
            if(is_array($positions)){
                foreach($positions as $position){
                    array_push($this->positions, new Position($position['id'], $position['alias'], $position['position'], $position['symbiont'], $position['accessLevel']));
                }
            }
        }
    }
    public function show(){
        foreach($this->positions as $position){
            $position->show();
        }
    }
    public function add($position){
        if(get_class($position)!='Position') return false;
        array_push($this->positions, $position);
        return true;
    }
    public function save(){
        global $db;
        if($this->id!=null||$this->alias!=null){
            $where=array();
            if($this->id!=null){
                $where['id']=$this->id;
            }
            else{
                $where['alias']=$this->alias;
            }
            if($this->id==null) $this->id=$db->select('positions', 'id', $where, '', 1);
            $db->delete('positions', $where);
        }
        foreach($this->positions as $position){
            if($this->id!=null) $position->id=$this->id;
            if($this->alias!=null) $position->alias=$this->alias;
            $position->save();
            $this->id=$position->id;
        }
    }
    public function get(){
        return $this->positions;
    }
}
?>