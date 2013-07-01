<?
abstract class Symbiont{
    private $symbiont;
    private $class;
    public function __construct($symbiont=null){
        global $labels, $kernel;
        if($symbiont==null){
            $symbiont=substr(get_class($this), 1);
        }
        $symbiont=strtolower($symbiont);
        $pos=strpos($symbiont, '_');
        if($pos!==false){
            $this->symbiont=substr($symbiont, 0, $pos);
            $this->class=substr($symbiont, $pos+1);
        }
        else{
            $this->symbiont=$symbiont;
            $this->class='';
        }
        $labels->import('!symbionts/'.$this->symbiont.'/labels/'.($this->class?$this->class.'/':''));
    }
    //Main function of the symbiont
    public abstract function main($template=null, $attributes=null, $content=null);
    //Creat corrent design
    public function _check($template, $default='main', $symbiont=''){
        global $kernel;
        if($symbiont==='') $symbiont=$this->symbiont;
        $dir='symbionts/'.$symbiont;
        if($template&&file_exists('!'.$dir.'/'.$template.'.des.html')){
            return $dir.'/'.$template;
        }
        else{
            return $dir.'/'.$default;
        }
    }
    
    //Save symbiont status
    public function _add($info=null){
        return $info->full;
    }
    public function _delete($info=null){
        return true;
    }
    public function _edit($info=null){
        return '';
    }

    //Administrate symbiont
    public function _admin($info=null){
        return;
    }
    
    //Get simple info about widget
    public function _info($info){
        return array('title'=>$info->full, 'block'=>false);
    }
    
    //Undefined functions call the main
    public function __call($name, $arguments){
        call_user_func_array(array($this, 'main'), $arguments);
    }
    
    //Get and set values
    public function __get($var){
        return isset($this->$var)?$this->$var:'';
    }
    public function __set($var, $val){
        $this->$var=$val;
    }
}
?>