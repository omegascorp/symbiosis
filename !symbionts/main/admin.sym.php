<?
class SMain_Admin extends Symbiont{
    public function __construct(){
        parent::__construct('Main');
    }
    public function main($template=null, $attributes=null, $content=null){
        global $design;
        $design->show('symbionts/main/admin');
    }
}
?>
