<?
//Style 0.0.1
class SStyle extends Symbiont{
    private $styles;
    public function __construct(){
        $this->styles=new Set();
    }
    public function main($design=null, $attributes=null, $content=null){
        global $kernel, $design;
        $content=$design->run($content);
        if(substr($content, 0, 7)!='http://') $content=$kernel->conf->css.$content;
        if($this->styles->push($content)){
            $kernel->vars->styles.='<link rel="stylesheet" href="'.$content.'" type="text/css" media="screen" />'."\r\n";
        }
    }
}
?>