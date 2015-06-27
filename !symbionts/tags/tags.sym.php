<?
class STags extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db, $symbionts;
        
        $attributes=Data::extend(array(
            'main'=>false
        ),$attributes);
        
        if($attributes['main']&&$kernel->link->param1){
            $template=$this->_check($template, 'tag');
            $alias=Data::safe($kernel->link->param1);
            
            $vars=array('items'=>array());
            $tags=$db->select('stags', array('id', 'title'), array('languageId'=>$kernel->lang->id, 'alias'=>$alias), '', 1);
            Place::push($tags['title']);
            $items=$db->select('stagsconnections', array('symbiont', 'itemId'), array('tagId'=>$tags['id']));
            if(is_array($items)){
                foreach($items as $item){
                    $kernel->addSymbiont($item['symbiont']);
                    array_push($vars['items'], $symbionts->$item['symbiont']->_link($item['itemId']));
                }
            }
            $design->show($template, $vars);
        }
        else{
            $template=$this->_check($template, 'main');
            $vars=array();
            $vars['tags']=$db->select('stags', array('id', 'alias', 'title', 'popularity'), array('languageId'=>$kernel->lang->id), 'popularity DESC', 20);
            $vars['max']=$db->query('SELECT MAX(popularity) FROM `stags` WHERE `languageId`='.$kernel->lang->id.' LIMIT 1', 1);
            $design->show($template, $vars);
        }
    }
    public function show($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db, $labels;
        $attributes=Data::extend(array(
            'symbiont'=>'',
            'itemId'=>0
        ),$attributes);
        
        $vars=array();
        $vars['symbiont']=Data::symbiont($attributes['symbiont']);
        $vars['itemId']=Data::number($attributes['itemId']);
        
        if($vars['itemId']){
            $vars['tags']=$db->query('
                SELECT t.id, t.title, t.alias
                FROM `stagsconnections` as c
                    LEFT JOIN `stags` as t
                ON t.id=c.tagId AND t.languageId='.$kernel->lang->id.'
                WHERE c.itemId='.$vars['itemId'].' AND c.symbiont="'.$vars['symbiont'].'"
                ORDER BY t.popularity DESC
            ');
        }
        else{
            $vars['tags']='';
        }
        $template=$this->_check($template, 'show');
        $design->show($template, $vars);
    }
    public function admin($template=null, $attributes=null, $content=null){
        global $kernel, $symbionts;
        $kernel->addSymbiont('Tags-Admin');
        $symbionts->TagsAdmin->main();
    }
    public function _edit($symbiont=null){
        global $design;
        $template='symbionts/tags/edit';
        
        $vars=array('main'=>false);
        if(isset($symbiont->attributes['main'])){
            $vars['main']=$symbiont->attributes['main'];
        }
        $design->show($template, $vars);
    }
    public function _info($symbiont=null){
        global $labels;
        
        $main=false;
        if(isset($symbiont->attributes['main'])){
            $main=$symbiont->attributes['main'];
        }
        
        $title=$labels->get('symbionts.tags');
        if($main) $title='# '.$title;
        
        return array('title'=>$title, 'block'=>false);
    }
}
?>