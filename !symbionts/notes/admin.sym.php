<?
class SNotes_Admin extends Symbiont{
    public function __construct(){
        parent::__construct('Notes');
    }
    public function main($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db, $symbionts, $labels;
        $attributes=Data::extend(array(
            
        ),$attributes);
        
        //Getting the notes
        if($kernel->link->param2){
            $attributes['categoryAlias']=$kernel->link->param2;
            $this->category($template, $attributes);
        }
        //Getting the category
        else{
            $kernel->addSymbiont('Categories-Admin');
            $symbionts->CategoriesAdmin->main($template, array('for'=>'notes', 'title'=>$labels->get('symbionts.notes')), null);
        }
    }
    public function category($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db, $symbionts, $labels;
        $attributes=Data::extend(array(
            'categoryAlias'=>''
        ),$attributes);
        $categoryAlias=Data::safe($attributes['categoryAlias']);
        $category=$db->select('scategories', array('id', 'title', 'settings'), array('languageId'=>$kernel->lang->id, 'alias'=>$categoryAlias), '', true);
            
        if(!$category['settings']){
            $category['settings']='{"order":"1","template":"blog","cover":{"width":"100","height":"100"},"path":"notes"}';
            $db->update('scategories', array('settings'=>$category['settings']), array('alias'=>$categoryAlias), '', 1);
        }
        
        if(is_array($category)){
            $settings=json_decode($category['settings']);
            $template=$this->_check($template, 'admin');
            
            $startQuery='';
            switch($settings->order){
                case 0: $order='position'; break;
                case 1: $order='date DESC'; break;
                case 2: $order='title';  break;
                default: $order='position';
            }
            
            $vars=array('title'=>$category['title'], 'id'=>$category['id']);
            $vars['sortable']=$settings->order==0;
            $vars['order']=$settings->order;
            $vars['path']=$settings->path;
            $design->show($template, $vars);
        }
        else{
            $symbionts->Admin->error(null, array(
                'label'=>'symbionts.notes.categoryNotExists',
                'vars'=>array(
                    'alias'=>$categoryAlias
                )
            ));
        }
    }
    public function notes($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db, $symbionts, $labels;
        $attributes=Data::extend(array(
            'id'=>0,
            'start'=>0,
            'limit'=>20,
            'order'=>0,
            'text'=>false
        ),$attributes);
        $id=Data::number($attributes['id']);
        $start=Data::safe($attributes['start']);
        $limit=Data::number($attributes['limit']);
        $order=Data::number($attributes['order']);
        $text=$attributes['text']?', text':'';
        
        $startQuery='';
        $orderQuery='';
        switch($attributes['order']){
            case 0: $orderQuery='position'; if($start) $startQuery='AND position>"'.$start.'"'; break;
            case 1: $orderQuery='date DESC'; if($start) $startQuery='AND date<"'.$start.'"'; break;
            case 2: $orderQuery='title'; if($start) $startQuery='AND title>"'.$start.'"'; break;
            default: $orderQuery='position'; if($start) $startQuery='AND position>"'.$start.'"';
        }
        $categoryQuery='';
        if($id) $categoryQuery='AND categoryId='.$id.'';
        
        $vars=array();
        $vars['sortable']=$order==0;
        $vars['notes']=$db->query('
            SELECT id, alias, title, date-NOW() as coming, date, position'.$text.'
            FROM `snotes`
            WHERE languageId='.$kernel->lang->id.' '.$categoryQuery.' '.$startQuery.'
            ORDER BY '.$orderQuery.'
            LIMIT '.$limit.'
        ');
        
        $template=$this->_check($template, 'admin-notes');
        $design->show($template, $vars);
    }
    public function change($template=null, $attributes=null, $content=null){
        $template=$this->_check($template, 'change');
        global $design, $db, $kernel;
        $attributes=Data::extend(array(
            'id'=>isset($_POST['id'])?Data::number($_POST['id']):0
        ),$attributes);
        $id=$attributes['id'];
        if($id){
            $vars=$db->query('
                SELECT  id, alias, title, cover, image,
                    DAY(date) as day, MONTH(date) as month, YEAR(date) as year, DATE_FORMAT(date, "%H") as hour, DATE_FORMAT(date, "%i") as minute
                FROM `snotes`
                WHERE id="'.$id.'" AND languageId="'.$kernel->lang->id.'"
            ', true);
            $vars['languages']=$db->query('
                SELECT
                    l.id,
                    l.abbr,
                    l.isDefault,
                    l.title as languageTitle,
                    m.alias,
                    m.title,
                    m.text
                FROM `languages` as l
                    LEFT JOIN `snotes` as m ON m.id='.$id.' AND m.languageId=l.id
                WHERE l.isEnabled=1
                ORDER BY l.position
            ');
        }
        else{
            $vars=$db->query('SELECT DAY(NOW()) as day, MONTH(NOW()) as month, YEAR(NOW()) as year,  DATE_FORMAT(NOW(), "%H") as hour, DATE_FORMAT(NOW(), "%i") as minute', true);
            $vars=Data::extend($vars, array(
                'id'=>$id,
                'title'=>'',
                'alias'=>'',
                'cover'=> '',
                'image'=> ''
            ));
            $vars['languages']=$db->query('
                SELECT
                    l.id,
                    l.abbr,
                    l.title as languageTitle,
                    l.isDefault,
                    "" AS alias,
                    "" AS title,
                    "" AS text
                FROM `languages` as l
                WHERE l.isEnabled=1
                ORDER BY l.position
            ');
        }
        
        $vars['aliasesInTranslit']=$kernel->conf->aliasesInTranslit;
        $vars['aliasesLanguage']=$kernel->conf->aliasesLanguage;
        $vars['abbreviations']=$kernel->conf->abbreviations;
        if($kernel->conf->aliasesInTranslit){
            if(count($vars['languages'])>1){
                foreach($vars['languages'] as $key=>$language){
                    $translit='db/translit/'.$language['abbr'].'.json';
                    if(file_exists($translit)){
                        $vars['languages'][$key]['translit']=file_get_contents($translit);
                    }
                    else{
                        $vars['languages'][$key]['translit']="{}";
                    }
                }
            }
            else{
                $translit='db/translit/'.$kernel->lang->abbr.'.json';
                $vars['translit']=file_get_contents($translit);
            }
        }
        $design->show($template, $vars);
    }
    public function delete($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        if($user->accessLevel<9){
            print($labels->get('errors.prerogatives'));
            return;
        }
        if(!isset($_POST['id'])){
            print($labels->get('errors.parametrs'));
            return;
        }
        if(is_array($_POST['id'])){
            $id=$_POST['id'];
            foreach($id as $key=>$val){
                $id[$key]=Data::number($val);
            }
        }
        else{
            $id=Data::number($_POST['id']);
        }
        $vars=array(
            'id'=>$id
        );
        $design->show('symbionts/notes/delete', $vars);
    }
    public function settings($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        if($user->accessLevel<9){
            return '<div>'.$labels->get('errors.prerogatives').'</div>';
        }
        if(!isset($_POST['id'])){
            return '<div>'.$labels->get('errors.parametrs').'</div>';
        }
        
        $template=$this->_check($template, 'settings');
        $id=Data::number($_POST['id']);
        $vars=array();
        $json=$db->select('scategories', 'settings', array('id'=>$id), '', 1);
        
        if(!$json){
            $json='{"order":"1","template":"blog","cover":{"width":"100","height":"100"},"path":"notes"}';
            $db->update('scategories', array('settings'=>$json), array('id'=>$id), '', 1);
        }
        $json=json_decode($json);
        $vars['order']=$json->order;
        
        if(!isset($json->template)) $json->template='';
        $folders=Data::read('!symbionts/notes/', '/main-.*/');
        $vars['templates']=array();
        foreach($folders as $val){
            $tmp=array();
            $val=substr($val, 5);
            $tmp['name']=$val;
            $tmp['title']=$labels->get('symbionts.notes.template'.ucfirst($val));
            $tmp['current']=($json->template==$val);
            array_push($vars['templates'], $tmp);
        }
        $vars['coverWidth']=$json->cover->width;
        $vars['coverHeight']=$json->cover->height;
        $vars['path']=$json->path;
        
        $design->show($template, $vars);
    }
    public function dbSettings($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['settings'])||!isset($_POST['id'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        $id=Data::number($_POST['id']);
        $json=$db->select('scategories', 'settings', array('id'=>$id), '', 1);
        $json=json_decode($json);
        $json->order=Data::number($_POST['settings']['order']);
        //$json->template=Data::safe($_POST['settings']['template']);
        //$json->cover=array();
        //$json->cover['width']=Data::number($_POST['settings']['coverWidth']);
        //$json->cover['height']=Data::number($_POST['settings']['coverHeight']);
        //$json->path=Data::fileSystem($_POST['settings']['path']);
        $db->update('scategories', array('settings'=>json_encode($json)), array('id'=>$id));
        
    }
    public function dbChange($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['categoryId'])||!isset($_POST['languages'])||!isset($_POST['date'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        
        $alias=isset($_POST['alias'])?Data::safe($_POST['alias']):'';
        $id=isset($_POST['id'])?Data::number($_POST['id']):'';
        $categoryId=Data::number($_POST['categoryId']);
        
        $cover=$db->select('snotes', 'cover', array('id'=>$id), '', 1);
        
        $json=$db->select('scategories', 'settings', array('id'=>$categoryId), '', 1);
        if(!$json){
            $json='{"order":"1","template":"grid"}}';
            $db->update('scategories', array('settings'=>$json), array('id'=>$categoryId), '', 1);
        }
        $json=json_decode($json);
        
        
        $values=array('categoryId'=>$categoryId);
        /*
        if(isset($_POST['image'])){
            if(file_exists($cover)) unlink($cover);
            $kernel->addLibrary('Image');
            $path='uploads'.Data::fileSystem($_POST['image']);
            $image=new Image($path);
            
            $values['cover']=$image->resize('.notes/{name}.'.$categoryId.'.{type}', $json->cover->width, $json->cover->height, false, true);
            $values['image']=$path;
            
        }
        */
        if(isset($_POST['languages'])){
            foreach($_POST['languages'] as $key=>$language){
                $where=array();
                $where['languageId']=Data::number($key);
                $where['id']=$id;
                
                $values['title']=Data::safe($language['title']);
                $values['alias']=isset($language['alias'])?Data::safe($language['alias']):$alias;
                $values['languageId']=$where['languageId'];
                $values['text']=Data::safe($language['content']);
                $values['date']=Data::safe($_POST['date']);
                
                if($id) $values['id']=$id;
                
                if($values['alias']==='') continue;
                $r=$db->insert('snotes', array_merge($values, array('userId'=>$user->id)));
                if(!$r){
                    if($id){
                        $r=$db->update('snotes', $values, $where);
                        $values['id']=$r;
                    }
                    else{
                        return '{"error":""}';
                    }
                }
                else{
                    $values['id']=$r;
                }
            }
            if(!isset($r)) return '{"error":""}';
            if(!$id){
                $db->update('snotes', array('position'=>$values['id']), array('id'=>$values['id']));
                $id=$values['id'];
            }
            /*
            if(isset($_POST['tags'])){
                $oldtags=$db->select('stagsconnections', array('tagId'), array('itemId'=>$id, 'symbiont'=>'Blog.main'));
                if(is_array($oldtags)){
                    foreach($oldtags as $val){
                        $tagId=Data::number($val['tagId']);
                        $db->update('stags', 'popularity=popularity-1', array('id'=>$tagId));
                    }
                }
                $db->delete('stagsconnections', array('itemId'=>$id, 'symbiont'=>'Blog.main'));
                if(is_array($_POST['tags'])){
                    foreach($_POST['tags'] as $val){
                        $tagId=Data::number($val);
                        $db->update('stags', 'popularity=popularity+1', array('id'=>$tagId));
                        $db->insert('stagsconnections', array('tagId'=>$tagId, 'itemId'=>$id, 'symbiont'=>'Blog.main'));
                    }
                }
            }
            */
            return '{"success":""}';
        }
        return '{"error":""}';
    }
    public function dbDelete($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['id'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        if(is_array($_POST['id'])){
            $ids=$_POST['id'];
            foreach($ids as $key=>$val){
                $id=Data::number($val);
                $oldtags=$db->select('stagsconnections', array('tagId'), array('itemId'=>$id, 'symbiont'=>'Blog.main'));
                if(is_array($oldtags)){
                    foreach($oldtags as $v){
                        $tagId=Data::number($v['tagId']);
                        $db->update('stags', 'popularity=popularity-1', array('id'=>$tagId));
                    }
                }
                $db->delete('stagsconnections', array('itemId'=>$id, 'symbiont'=>'Blog.main'));
                $db->delete('snotes', array('id'=>$id));
            }
        }
        else{
            $id=Data::number($_POST['id']);
            $oldtags=$db->select('stagsconnections', array('tagId'), array('itemId'=>$id, 'symbiont'=>'Blog.main'));
            if(is_array($oldtags)){
                foreach($oldtags as $v){
                    $tagId=Data::number($v['tagId']);
                    $db->update('stags', 'popularity=popularity-1', array('id'=>$tagId));
                }
            }
            $db->delete('stagsconnections', array('itemId'=>$id, 'symbiont'=>'Blog.main'));
            $db->delete('snotes', array('id'=>$id));
        }
        print('{"success":""}');
    }
    public function dbSort($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['sort'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        foreach($_POST['sort'] as $key=>$val){
            $id=data::number($val);
            $position=data::number($key)+1;
            $db->update('snotes', array('position'=>$position), array('id'=>$id));
        }
    }
    public function _admin($info=null){
        global $db, $design, $kernel;
        $info->attributes=Data::extend(array(
            'id'=>0
        ), $info->attributes);
        
        $vars=array('function'=>$info->function, 'id'=>$info->attributes['id']);
        
        if(file_exists('db/symbiont-notes/defaults.json')){
            $defaults=json_decode(file_get_contents('db/symbiont-notes/defaults.json'));
            $vars['default']=$defaults[$kernel->lang->id];
        }
        else{
            $vars['default']='';
        }
        
        
        $design->show('symbionts/notes/_admin', $vars);
    }
    public function dbDefault($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($attributes['id'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        $id=Data::number($attributes['id']);
        
        Data::clearFolder('db/symbiont-notes/');
        $pages=$db->query('
            SELECT p.alias, p.languageId
            FROM pages as p
            WHERE p.id='.$id.'
        ');
        $arr=array(0=>$id);
        foreach($pages as $page){
            $arr[$page['languageId']]=$page['alias'];
        }
        
        $f=fopen('db/symbiont-notes/defaults.json', 'w');
        fwrite($f, json_encode($arr));
        fclose($f);
        
        $db->update('pages', array('symbiont'=>'Notes'), array('id'=>$id));
        return '{"success":""}';
    }
    public function widgetAdmin($template=null, $attributes=null, $content=null){
        global $design;
        $design->show('symbionts/notes/widget-admin');
    }
}
?>