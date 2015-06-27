<?
//Languages 0.0.13
class SLanguages extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design;
        $template=$this->_check($template, 'main');
        $languages=$db->query("
            SELECT l.id, l.abbr, l.title, l.isDefault, p.alias, p.isHome
            FROM languages as l
                LEFT JOIN pages as p ON p.id=".$kernel->page->id." AND p.languageId=l.id
            WHERE l.isEnabled=1
            ORDER BY l.position
        ");
        $type=0;
        if(isset($attributes['type'])){
            switch($attributes['type']){
                case 'titles': $type=1; break;
                case 'abbrs': $type=2; break;
            }
        }
        $case=0;
        if(isset($attributes['case'])){
            switch($attributes['case']){
                case 'normal': $case=0; break;
                case 'lower': $case=1; break;
                case 'upper': $case=2; break;
                default: $case=0;
            }
        }
        $vars=array();
        if(is_array($languages)){
            $vars['languages']=array();
            foreach($languages as $language){
                $lang=array();
                $lang['id']=$language['id'];
                $lang['title']=$type?$language['title']:$language['abbr'];
                $lang['url']=$kernel->conf->url;
                $lang['exists']=false;
                $lang['current']=false;
                
                switch($case){
                    case 1: $lang['title']=strtolower($lang['title']); break;
                    case 2: $lang['title']=strtoupper($lang['title']); $case=2; break;
                }
                if($language['alias']!=null){
                    $lang['exists']=true;
                    if($language['abbr']==$kernel->lang->abbr) $lang['current']=true;
                    
                }
                if($kernel->conf->abbreviations){
                    $lang['url'].=$language['abbr'].'/';
                }
                if(!$language['isHome']||$kernel->link->params){
                    $lang['url'].=$language['alias'].'/';
                }
                if($kernel->link->relative&&$kernel->link->params){
                    $lang['url'].=$kernel->link->params.$kernel->conf->postfix;
                }
                array_push($vars['languages'], $lang);
            }
            $design->show($template, $vars);
        }
    }
    public function admin($template=null, $attributes=null, $content=null){
        global $kernel, $symbionts;
        $kernel->addSymbiont('Languages-Admin');
        $symbionts->LanguagesAdmin->main($template=null, $attributes=null, $content=null);
    }
}
?>