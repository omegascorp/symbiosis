<?
//Main 0.0.4
class SMain extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $kernel, $design, $symbionts, $labels, $user;
        if(!$template) $template='templates/'.$kernel->page->template;
        else $template='templates/'.$template;
        $folder=substr($template, 0, strrpos($template, '/'));
        $labels->import('!'.$folder.'/labels/');
        $vars=array();
        $symbiont=$kernel->page->symbiont;
        if($symbiont){
            $vars['content']=Design::symbiontGet($symbiont);
        }
        else{
            $vars['content']='';
        }
        $design->show($template, $vars);
    }
    public function admin($template=null, $attributes=null, $content=null){
        global $kernel, $symbionts;
        $kernel->addSymbiont('Main-Admin');
        $symbionts->MainAdmin->main();
    }
}
?>
