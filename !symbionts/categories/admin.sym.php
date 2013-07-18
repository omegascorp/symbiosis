<?
class SCategories_Admin extends Symbiont{
    public function __construct(){
        parent::__construct('Categories');
    }
    public function main($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db;
        $attributes=Data::extend(array(
            'for'=>'',
            'title'=>'',
            'settings'=>''
        ),$attributes);
        
        $vars=array('for'=>$attributes['for'], 'title'=>$attributes['title']);
        $vars['settings']=$attributes['settings'];
        
        
        $template=$this->_check($template, 'admin');
        $design->show($template, $vars);
    }
    public function categories($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db;
        $attributes=Data::extend(array(
            'for'=>'',
            'settings'=>'',
            'parentId'=>0,
            'level'=>0,
            'ignoreId'=>0,
            'selectId'=>0
        ),$attributes);
        $for=Data::safe($attributes['for']);
        $settings=Data::safe($attributes['settings']);
        $parentId=Data::number($attributes['parentId']);
        $level=Data::number($attributes['level']);
        $selectId=Data::number($attributes['selectId']);
        $ignoreId=Data::number($attributes['ignoreId']);
        $vars=array('for'=>$for, 'template'=>$template, 'parentId'=>$parentId, 'selectId'=>$selectId, 'ignoreId'=>$ignoreId);
        //$vars['categories']=$db->select('scategories', array('id', 'alias', 'title', 'for', 'settings'), array('languageId'=>$kernel->lang->id, 'for'=>$for, 'parentId'=>$parentId), 'position');
        $vars['categories']=$db->query("
            SELECT c.id, c.alias, c.title, c.for, c.settings, (
                SELECT 1
                FROM `scategories` as s
                WHERE s.languageId=".$kernel->lang->id." AND s.for='".$for."' AND s.parentId=c.id
            ) as haveSub
            FROM `scategories` as c
            WHERE c.languageId=".$kernel->lang->id." AND c.for='".$for."' AND c.parentId=".$parentId."
            ORDER BY c.position
        ");
        $vars['settings']=$settings;
        $vars['level']=$level;
        if(is_array($vars['categories'])){
            foreach($vars['categories'] as $key=>$val){
                $settings=json_decode($val['settings']);
                if(isset($settings->system)) $vars['categories'][$key]['system']=true;
                else $vars['categories'][$key]['system']=false;
            }
        }
        
        $template=$this->_check($template, 'admin-categories');
        $design->show($template, $vars);
    }
    public function change($template=null, $attributes=null, $content=null){
        $template=$this->_check($template, 'change');
        global $design, $db, $kernel;
        $attributes=Data::extend(array(
            'for'=>''
        ),$attributes);
        $id=isset($_POST['id'])?Data::number($_POST['id']):0;
        if($id){
            $vars=$db->select('scategories', array('id', 'alias', 'title', 'for', 'parentId'), array('id'=>$id, 'languageId'=>$kernel->lang->id), '', 1);
            $vars['languages']=$db->query('
                SELECT
                    l.id,
                    l.abbr,
                    l.isDefault,
                    l.title as languageTitle,
                    m.alias,
                    m.title
                FROM `languages` as l
                    LEFT JOIN `scategories` as m ON m.id='.$id.' AND m.languageId=l.id
                WHERE l.isEnabled=1
                ORDER BY l.position
            ');
        }
        else{
            $vars=array(
                'id'=>$id,
                'title'=>'',
                'alias'=>'',
                'for'=>$attributes['for'],
                'parentId'=>0
            );
            $vars['languages']=$db->query('
                SELECT
                    l.id,
                    l.abbr,
                    l.isDefault,
                    l.title as languageTitle,
                    "" AS alias,
                    "" AS title
                FROM `languages` as l
                WHERE l.isEnabled=1
                ORDER BY l.position
            ');
        }
        
        $design->show($template, $vars);
    }
    public function delete($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        $labels->import('db/labels/errors');
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
        $design->show('symbionts/categories/delete', $vars);
    }
    public function dbChange($template=null, $attributes=null, $content=null){
        global $kernel, $db, $labels;
        $alias=isset($_POST['alias'])?Data::safe($_POST['alias']):'';
        $id=isset($_POST['id'])?Data::number($_POST['id']):'';
        
        if($id) $settings=$db->select('scategories', 'settings', array('id'=>$id), '', 1);
        $values=array('for'=>Data::safe($_POST['for']), 'parentId'=>Data::safe($_POST['parentId']));
        $success=false;
        if(isset($_POST['languages'])){
            foreach($_POST['languages'] as $key=>$language){
                if(!$alias&&!isset($language['alias'])) continue;
                $where=array();
                $where['languageId']=Data::number($key);
                $where['id']=$id;
                
                $values['title']=Data::safe($language['title']);
                $values['alias']=isset($language['alias'])?Data::safe($language['alias']):$alias;
                $values['languageId']=$where['languageId'];
                if($id){
                    $values['id']=$id;
                    $values['settings']=$settings;
                }
                
                if($values['alias']==='') continue;
                $r=$db->insert('scategories', $values);
                if(!$r){
                    if($db->update('scategories', $values, $where)){
                        $success=true;
                    }
                }
                else{
                    $success=true;
                    $values['id']=$r;
                }
            }
            if(!$id&&isset($values['id'])){
                $success=true;
                $db->update('scategories', array('position'=>$values['id']), array('id'=>$values['id']));
            }
            if($success) return '{"success":""}';
            else return '{"error":"'.$labels->get('symbionts.categories.error').'"}';
        }
        elseif(isset($_POST['title'])&&isset($_POST['alias'])){
            $where['languageId']=$kernel->lang->id;
            $where['id']=$id;
            
            $values['title']=Data::safe($_POST['title']);
            $values['alias']=Data::safe($_POST['alias']);
            $values['languageId']=$kernel->lang->id;
            
            $r=$db->insert('scategories', $values);
            if(!$r){
                $db->update('scategories', $values, $where);
            }
            else{
                $db->update('scategories', array('position'=>$r), array('id'=>$r));
            }
            
            return '{"success":""}';
        }
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
                $db->delete('scategories', array('id'=>$id));
            }
        }
        else{
            $id=Data::number($_POST['id']);
            $db->delete('scategories', array('id'=>$id));
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
            $db->update('scategories', array('position'=>$position), array('id'=>$id), 1);
        }
    }
}
?>