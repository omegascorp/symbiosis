<?
//Script 0.0.1
class SScript extends Symbiont{
    private $scripts;
    private $ajax=false;
    public function __construct(){
        $this->scripts=new Set();
    }
    public function main($design=null, $attributes=null, $content=null){
        global $kernel, $design;
        $content=$design->run($content);
        if(substr($content, 0, 7)!='http://') $content=$kernel->conf->js.$content;
        if($this->scripts->push($content)){
            if(!$this->ajax) $kernel->vars->scripts.='<script type="text/javascript" src="'.$content.'"></script>'."\r\n";
            else $kernel->vars->scripts.='<script type="text/javascript">$.getScript("'.$content.'");</script>'."\r\n";
        }
    }
    public function ajax($t=true){
        $this->ajax=$t;
    }
}
?>