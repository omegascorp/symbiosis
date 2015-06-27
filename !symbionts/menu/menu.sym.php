<?
class SMenu extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db;
        $attributes=Data::extend(array(
            'id'=>0,
            'alias'=>''
        ), $attributes);
        $template=$this->_check($template, 'main');
        $id=$attributes['id'];
        $alias=Data::safe($attributes['alias']);
        if($alias){
            $id=$db->select('smenu', 'id', array('alias'=>$alias), '', 1);
        }
        $vars=array('id'=>$id);
        $design->show($template, $vars);
    }
    /*
    public function items($template=null, $attributes=null, $content=null){
        $attributes=Data::extend(array(
            'selectedId'=>0,
            'neglectId'=>0,
            'id'=>0
        ), $attributes);
        //print $this->getItems($attributes['id'], 0, $attributes['selectedId'], $attributes['neglectId']);
    }
    */
    public function items($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design;
        $attributes=Data::extend(array(
            'id'=>0,
            'selectId'=>0,
            'ignoreId'=>0,
            'level'=>0,
            'parentId'=>0
        ), $attributes); 
        
        $id=Data::number($attributes['id']);
        $selectId=Data::number($attributes['selectId']);
        $ignoreId=Data::number($attributes['ignoreId']);
        $level=Data::number($attributes['level']);
        $parentId=Data::number($attributes['parentId']);
        
        $vars=array(
            'template'=>$template,
            'id'=>$id,
            'selectId'=>$selectId,
            'ignoreId'=>$ignoreId,
            'level'=>$level,
            'parentId'=>$parentId
        );
        
        $vars['items']=$db->query('
            SELECT i.id, i.title, i.link, "" as external, (
                SELECT 1
                FROM `smenuitems` as si
                WHERE si.languageId='.$kernel->lang->id.' AND si.menuId='.$id.' AND si.parentId=i.id
                LIMIT 1
                ) as sub
            FROM `smenuitems` as i
            WHERE i.languageId='.$kernel->lang->id.' AND i.menuId='.$id.' AND i.parentId='.$parentId.'
            ORDER BY `position`
        ');
        
        foreach($vars['items'] as $itemIndex=>$item){
            if(substr($item['link'], 0, 7)=='http://'||substr($item['link'], 0, 8)=='https://'){
                $vars['items'][$itemIndex]['external']=true;
            }
            $vars['items'][$itemIndex]['current']=false;
            if($item['link']==$kernel->link->full||
               $item['link']==$kernel->lang->abbr."/".$kernel->page->alias."/"||
               $item['link']==$kernel->page->alias."/"||
               ($item['link']=="/"||$item['link']==$kernel->lang->abbr."/")&&$kernel->page->isHome){
                $vars['items'][$itemIndex]['current']=true;
            }
        }
        
        $template=$this->_check($template, 'items');
        $design->show($template, $vars);
    }
    public function admin($template=null, $attributes=null, $content=null){
        global $kernel, $symbionts;
        $kernel->addSymbiont('Menu-Admin');
        $symbionts->MenuAdmin->main($template=null, $attributes=null, $content=null);
    }
    public function _edit($info=null){
        global $kernel, $design, $db;
        $info->attributes=Data::extend(array(
            'id'=>''
        ), $info->attributes);
        $vars=array();
        $vars['id']=Data::number($info->attributes['id']);
        $vars['menus']=$db->select('smenu', array('title', 'id'), array('languageId'=>$kernel->lang->id), 'position');
        $design->show('symbionts/menu/edit', $vars);
    }
    public function _info($info=null){
        global $db, $kernel, $labels;
        $info->attributes=Data::extend(array(
            'id'=>''
        ), $info->attributes);
        $id=Data::number($info->attributes['id']);
        $title=$labels->get('symbionts.menu');
        if($id){
            $title.=' → ';
            $title.=$db->select('smenu', 'title', array('id'=>$id, 'languageId'=>$kernel->lang->id), '', 1);
        }
        if(!$title){
            $title=$info->full;
        }
        return array('title'=>$title, 'block'=>false);
    }
}
?>
