<?
class SCaptcha extends symbiont{
    public function main($template='', $attributes='', $content=''){
        global $design, $kernel;
        $uniq=Data::uniq(6);
        
        $template=$this->_check($template, 'main');
        $design->show($template, array('uniq'=>$uniq));
    }
}
?>