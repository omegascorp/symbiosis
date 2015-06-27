<?
class Position{
    private $id;
    private $alias;
    private $position;
    private $symbiont;
    private $accessLevel;
    private $isExists;
    public function __construct($id, $alias=null, $position=1, $symbiont='', $accessLevel=0){
        global $db;
        $this->id=$id;
        $this->alias=$alias;
        $this->position=$position;
        $this->symbiont=$symbiont;
        $this->accessLevel=$accessLevel;
        $this->isExists=null;
    }
    public function read(){
        global $db;
        $where=array('position'=>$this->position);
        if($this->id){
            $where['id']=$this->id;
        }
        else{
            $where['alias']=$this->alias;
        }
        $position=$db->select('position', array('id', 'alias', 'symbiont', 'accessLevel'), $where, '', 1);
        if(is_array($position)){
            $this->id=$position['id'];
            $this->alias=$position['alias'];
            $this->symbiont=$position['symbiont'];
            $this->accessLevel=$position['accessLevel'];
            $this->isExists=true;
        }
        else{
            $this->isExists=false;
        }
    }
    public function isExists(){
        global $db;
        $where=array('position'=>$this->position);
        if($this->id){
            $where['id']=$this->id;
        }
        else{
            $where['alias']=$this->alias;
        }
        if($db->count('positions', $where, 1)){
            $this->isExists=true;
        }
        else{
            $this->isExists=false;
        }
        return $this->isExists;
    }
    public function show(){
        Design::symbiontEval($this->symbiont);
    }
    public function save(){
        global $db;
        $values=array();
        if($this->alias!=null) $values['alias']=$this->alias;
        $values['symbiont']=$this->symbiont;
        $values['accessLevel']=$this->accessLevel;
        $where=array('position'=>$this->position);
        if($this->id!=null){
            $where['id']=$this->id;
        }
        $result=$db->insert('positions', array_merge($values, $where));
        if($result){
            $this->id=$result;
        }
        else{
            if($this->id===null){
                $this->id=$db->select('positions', 'id', array('alias'=>$this->alias), '', 1);
            }
            $where['id']=$this->id;
            $result=$db->update('positions', $values, $where);
        }
        return $result?true:false;
    }
    public function delete(){
        global $db;
        $where=array('position'=>$this->position);
        if($this->id){
            $where['id']=$this->id;
        }
        else{
            $where['alias']=$this->alias;
        }
        return $db->delete('positions', $where);
    }
    public function __get($key){
        return isset($this->$key)?$this->$key:'';
    }
    public function __set($key, $val){
        $this->$key=$val;
    }
}
?>