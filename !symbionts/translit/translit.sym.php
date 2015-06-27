<?
class STranslit extends Symbiont{
    public function main($template='', $attributes='', $content=''){
        global $kernel, $design;
        $vars=array();
        if($kernel->conf->aliasesInTranslit){
            $languages=Data::read('db/translit/');
            foreach($languages as $language){
                $language=substr($language, 0, strlen($language)-5);
                $translit='db/translit/'.$language.'.json';
                if(file_exists($translit)){
                    $vars['languages'][$language]=file_get_contents($translit);
                }
                else{
                    $vars['languages'][$language]="{}";
                }
            }
        }
        $template=$this->_check('translit');
        $design->show($template, $vars);
    }
}
?>