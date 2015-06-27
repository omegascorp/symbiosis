<?
class SText extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $kernel, $design;
        $id=$kernel->page->id;
        $language=$kernel->lang->abbr;
        $file='db/symbiont-text/'.$id.'-'.$language.'.html';
        $vars=array();
        
        if(file_exists($file)) $vars['content']=stripslashes(file_get_contents($file));
        else $vars['content']='';
        $template=$this->_check($template, 'main');
        $design->show($template, $vars);
    }
    public function _admin($info=null){
        global $kernel, $symbionts;
        $kernel->addSymbiont('Text-Admin');
        $symbionts->TextAdmin->_admin($info);
    }
}
?>