<?
class SNotes extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db, $symbionts, $labels;
        $attributes=Data::extend(array(
            'categoryId'=>0
        ), $attributes);
        $categoryId=Data::number($attributes['categoryId']);
        
        if($kernel->link->param1=='note'){
            $alias=$kernel->link->param2;
            $this->note($template, array('alias'=>$alias, 'place'=>true), $content);
        }
        elseif($kernel->link->param1=='category'){
            $alias=$kernel->link->param2;
            $this->category($template, array('alias'=>$alias, 'place'=>true), $content);
        }
        else{
            $this->category($template, array('id'=>0, 'place'=>false), $content);
        }
    }
    public function note($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design, $symbionts, $labels;
        $attributes=Data::extend(array(
            'id'=>0,
            'alias'=>'',
            'place'=>false,
            'relative'=>false
        ), $attributes);
        
        $id=$attributes['id'];
        $alias=$attributes['alias'];
        
        $values=array('languageId'=>$kernel->lang->id);
        if($id) $values['id']=$attributes['id'];
        elseif($alias) $values['alias']=$attributes['alias'];
        
        $vars=$db->select('snotes', array('title', 'text', 'alias', 'id', 'date', 'userId', 'categoryId', 'image'), $values, '', 1);
        if(!is_array($vars)){
            $template=$this->_check('nofound');
            $design->show($template, array('message'=>$labels->get('symbionts.notes.noteNoFound')));
            return;
        }
        
        $vars['category']=$db->select('scategories', array('title', 'alias'), array('id'=>$vars['categoryId'], 'languageId'=>$kernel->lang->id), 1);
        $vars['autor']=$db->select('users', array('username', 'firstName', 'lastName', 'middleName'), array('id'=>$vars['userId']), '', 1);
        $vars['date']=preg_replace("/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/", "$3.$2.$1 $4:$5", $vars['date']);
        
        if(file_exists('db/symbiont-notes/defaults.json')){
            $defaults=json_decode(file_get_contents('db/symbiont-notes/defaults.json'));
            $vars['path']=$defaults[$kernel->lang->id];
        }
        else{
            $vars['path']='';
        }
        
        if($attributes['place']){
            if(!$attributes['relative']) Place::push($vars['category']['title'], 'category/'.$vars['category']['alias']);
            Place::push($vars['title']);
        }
        
        $template=$this->_check('-'.$template.'/note', '-main/note');
        
        $design->show($template, $vars);
    }
    public function notes($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design, $symbionts, $labels;
        
        $attributes=Data::extend(array(
            'id'=>0,
            'page'=>0,
            'limit'=>10,
            'order'=>0,
            'place'=>false
        ), $attributes);
        
        $id=Data::number($attributes['id']);
        $page=Data::safe($attributes['page']);
        $limit=Data::number($attributes['limit']);
        
        /*
        $categoryQuery='';
        if($id) $categoryQuery='AND categoryId='.$id;
        */
        $startQuery='';
        switch($attributes["order"]){
            case 0: $orderQuery='position'; if($start) $startQuery='AND position>"'.$start.'"'; break;
            case 1: $orderQuery='date DESC'; if($start) $startQuery='AND date<"'.$start.'"'; break;
            case 2: $orderQuery='title'; if($start) $startQuery='AND title>"'.$start.'"'; break;
            default: $orderQuery='position'; if($start) $startQuery='AND position>"'.$start.'"';
        }
        $vars=array();
        $startQuery.=$id?' AND categoryId='.$id:'';
        $vars['notes']=$db->query('
            SELECT id, alias, title, date, text, cover, position
            FROM `snotes`
            WHERE languageId='.$kernel->lang->id.' AND date<=NOW()
            ORDER BY '.$orderQuery.'
            LIMIT '.$page*$limit.', '.$limit.'
        ');
        
        if(file_exists('db/symbiont-notes/defaults.json')){
            $defaults=json_decode(file_get_contents('db/symbiont-notes/defaults.json'));
            $vars['path']=$defaults[$kernel->lang->id];
        }
        else{
            $vars['path']='';
        }
        
        if(is_array($vars['notes'])){
            foreach($vars['notes'] as $key=>$val){
                $vars['notes'][$key]['text']=Data::p($val['text'], 1);
                $date=preg_replace("/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/", "$3.$2.$1 $4:$5", $val['date']);
                
                $vars['notes'][$key]['dateOriginal']=$val['date'];
                $vars['notes'][$key]['date']=$date;
            }
        }
        
        $template=$this->_check('-'.$template.'/notes', '-main/notes');
        $design->show($template, $vars);
    }
    public function category($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design, $symbionts, $labels;
        
        $attributes=Data::extend(array(
            'id'=>0,
            'alias'=>'',
            'place'=>false,
            'start'=>0,
            'limit'=>20
        ), $attributes);
        $id=Data::number($attributes['id']);
        $alias=Data::safe($attributes['alias']);
        $start=Data::safe($attributes['start']);
        $limit=Data::number($attributes['limit']);
        
        $values=array('languageId'=>$kernel->lang->id);
        if($id) $values['id']=$id;
        elseif($alias) $values['alias']=$alias;
        
        if($id||$alias){
            $category=$db->select('scategories', array('title', 'settings', 'id'), $values, '', 1);
            if(!is_array($category)){
                $template=$this->_check('-'.$template.'/nofound', '-main/nofound');
                $design->show($template, array('message'=>$labels->get('symbionts.notes.categoryNoFound')));
                return;
            }
            $id=$category['id'];
            
            if($attributes['place']) Place::push($category['title']);
            $vars=array('title'=>$category['title']);
            $settings=json_decode($category['settings']);
        }
        else{
            $vars=array('title'=>'');
            $settings=array();
            $id=0;
        }
        
        
        
        $vars['template']=$template;
        $vars['order']=isset($settings->order)?$settings->order:1;
        $vars['id']=$id;
        
        $template=$this->_check('-'.$template.'/category', '-main/category');
        $design->show($template, $vars);
    }
    public function pageCategory($template=null, $attributes=null, $content=null){
        global $kernel;
        $attributes=Data::extend(array(
            'id'=>0,
            'alias'=>'',
            'place'=>false,
            'start'=>0,
            'limit'=>20
        ), $attributes);
        
        if($kernel->link->param1!=''){
            $this->note($template, array('alias'=>$kernel->link->param1, 'place'=>true, 'relative'=>true), $content);
        }
        else{
            $this->category($template, $attributes, $content);
        }
    }
    public function categories($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design;
        $attributes=Data::extend(array(
            'id'=>0,
            'level'=>0
        ), $attributes);
        
        $id=Data::number($attributes['id']);
        $level=Data::number($attributes['level']);
        
        $vars=array();
        //$vars['categories']=$db->select('scategories', array('title', 'alias'), array('languageId'=>$kernel->lang->id, 'parentId'=>$id, 'for'=>'Notes'), 'position');
        $vars['categories']=$db->query("
            SELECT c.title, c.alias, c.id, (
                SELECT 1
                FROM scategories as sub
                WHERE sub.parentId=c.id
                LIMIT 1
            ) as sub
            FROM scategories as c
            WHERE c.languageId=".$kernel->lang->id." AND c.parentId=".$id." AND c.for='Notes'
            ORDER BY c.position
        ");
        
        $vars['id']=$id;
        $vars['level']=$level;
        $vars['template']=$template;
        
        if(file_exists('db/symbiont-notes/defaults.json')){
            $defaults=json_decode(file_get_contents('db/symbiont-notes/defaults.json'));
            $vars['path']=$defaults[$kernel->lang->id];
        }
        else{
            $vars['path']='';
        }
        
        $template=$this->_check($template.'/categories', 'main/categories');
        $design->show($template, $vars);
    }
    public function admin($template=null, $attributes=null, $content=null){
        global $kernel, $symbionts;
        $kernel->addSymbiont('Notes-Admin');
        $symbionts->NotesAdmin->main($template);
    }
    public function _admin($info=null){
        global $kernel, $symbionts;
        $kernel->addSymbiont('Notes-Admin');
        $symbionts->NotesAdmin->_admin($info);
    }
    public function _edit($info=null){
        global $db, $design, $kernel;
        $info->attributes=Data::extend(array(
            'categoryId'=>0,
            'noteId'=>0
        ), $info->attributes);
        $categoryId=Data::number($info->attributes['categoryId']);
        $noteId=Data::number($info->attributes['noteId']);
        
        $vars=array('categoryId'=>$categoryId, 'noteId'=>$noteId);
        $vars['categories']=$db->select('scategories', array('title', 'id'), array('for'=>'notes', 'languageId'=>$kernel->lang->id), 'position');
        
        $design->show('symbionts/notes/edit', $vars);
    }
    public function editNotes($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        if($user->accessLevel<9){
            return($labels->get('errors.prerogatives'));
        }
        if(!isset($_POST['categoryId'])){
            return($labels->get('errors.parametrs'));
        }
        $categoryId=Data::number($_POST['categoryId']);
        $notes=$db->select('snotes', array('title', 'id'), array('categoryId'=>$categoryId, 'languageId'=>$kernel->lang->id), 'position');
        return json_encode($notes);
    }
    public function _info($info=null){
        global $db, $kernel, $labels;
        $info->attributes=Data::extend(array(
            'categoryId'=>'',
            'noteId'=>''
        ), $info->attributes);
        $categoryId=Data::number($info->attributes['categoryId']);
        $noteId=Data::number($info->attributes['noteId']);
        $title=$labels->get('symbionts.notes');
        if($categoryId){
            $title.=' → ';
            $title.=$db->select('scategories', 'title', array('id'=>$categoryId, 'languageId'=>$kernel->lang->id), '', 1);
        }
        if($noteId){
            $title.=' → ';
            $title.=$db->select('snotes', 'title', array('id'=>$noteId, 'languageId'=>$kernel->lang->id), '', 1);
        }
        if(!$title){
            $title=$info->full;
        }
        return array('title'=>$title, 'block'=>false);
    }
    public function _link($itemId=0){
        global $kernel, $db;
        $page=$kernel->conf->base.$kernel->conf->pageBlog."/";
        $itemId=Data::number($itemId);
        $note=$db->query("
            SELECT title, CONCAT('".$page."', alias, '/') as link
            FROM `snotes`
            WHERE languageId=".$kernel->lang->id." AND id=".$itemId."
            LIMIT 1
        ", true);
        return $note;
    }
}
?>