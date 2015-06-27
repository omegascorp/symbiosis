<?
//Symbionts 0.1.3
class Symbionts{
    function add($var, $symbiont, $class){
        //$symbiont=ucfirst($symbiont);
        //$class=ucfirst($class);
        $name="S".($symbiont!=$class?$symbiont.'_'.$class:$symbiont);
        $this->$var=new $name();
    }
}
?>