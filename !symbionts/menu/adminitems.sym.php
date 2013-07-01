<?
class SMenu_AdminItems extends Symbiont{
    private $id=0;
    private $title;
    public function __construct(){
        parent::__construct('menu');
        global $kernel, $db;
        $alias=Data::word($kernel->link->param2);
        $menu=$db->select('smenu', array('title', 'id'), array('languageId'=>$kernel->lang->id, 'alias'=>$alias), '', 1);
        if(!is_array($menu)) return;
        $this->id=$menu['id'];
        $this->title=$menu['title'];
    }
    public function main($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db;
        $template=$this->_check($template, 'admin-items');
        
        $vars=array('title'=>$this->title, 'id'=>$this->id);
        $design->show($template, $vars);
    }
    public function items($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design;
        $attributes=Data::extend(array(
            'selectId'=>0,
            'neglectId'=>0,
            'parentId'=>0,
            'level'=>0
        ), $attributes);
        $selectId=Data::number($attributes['selectId']);
        $neglectId=Data::number($attributes['neglectId']);
        $parentId=Data::number($attributes['parentId']);
        $level=Data::number($attributes['level']);
        $items=$db->query('
            SELECT i.id, i.title, (
                SELECT 1
                FROM `smenuitems` as si
                WHERE si.languageId='.$kernel->lang->id.' AND si.menuId='.$this->id.' AND si.parentId=i.id
                LIMIT 1
                ) as sub
            FROM `smenuitems` as i
            WHERE i.languageId='.$kernel->lang->id.' AND i.menuId='.$this->id.' AND i.parentId='.$parentId.'
            ORDER BY `position`
        ');
        $vars=array();
        $vars['items']=$items;
        $vars['selectId']=$selectId;
        $vars['neglectId']=$neglectId;
        $vars['parentId']=$parentId;
        $vars['level']=$level;
        $vars['template']=$template;
        $template=$this->_check($template, 'admin-items-item');
        $design->show($template, $vars);
    }
    public function change($template=null, $attributes=null, $content=null){
        $template=$this->_check($template, 'change-item');
        global $design, $db, $kernel;
        $id=isset($_POST['id'])?Data::number($_POST['id']):0;
        if($id){
            $vars=$db->select('smenuitems', array('id', 'title', 'link', 'parentId'), array('id'=>$id, 'languageId'=>$kernel->lang->id), '', 1);
            $vars['languages']=$db->query('
                SELECT
                    l.id,
                    l.abbr,
                    l.isDefault,
                    l.title as languageTitle,
                    m.link,
                    m.title
                FROM `languages` as l
                    LEFT JOIN `smenuitems` as m ON m.id='.$id.' AND m.languageId=l.id
                WHERE l.isEnabled=1
                ORDER BY l.position
            ');
        }
        else{
            $vars=array(
                'id'=>$id,
                'title'=>'',
                'link'=>''
            );
            $vars['languages']=$db->query('
                SELECT
                    l.id,
                    l.abbr,
                    l.isDefault,
                    l.title as languageTitle,
                    "" AS title,
                    "" AS link
                FROM `languages` as l
                WHERE l.isEnabled=1
                ORDER BY l.position
            ');
        }
        $vars['menuId']=$this->id;
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
        $design->show('symbionts/menu/delete', $vars);
    }
    public function dbChange($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        if($user->accessLevel<9){
            print($labels->get('errors.prerogatives'));
            return;
        }
        if(!isset($_POST['parentId'])||!isset($_POST['menuId'])){
            print($labels->get('errors.parametrs'));
            return;
        }
        $id=isset($_POST['id'])?Data::number($_POST['id']):'';
        $menuId=Data::number($_POST['menuId']);
        
        $values=array();
        $values['parentId']=Data::number($_POST['parentId']);
        $values['menuId']=Data::number($_POST['menuId']);
        
        if(isset($_POST['languages'])){
            foreach($_POST['languages'] as $key=>$language){
                $where=array();
                $where['languageId']=Data::number($key);
                $where['id']=$id;
                
                $values['title']=Data::safe($language['title']);
                $values['link']=Data::safe($language['link']);
                
                $values['languageId']=$where['languageId'];
                if($id) $values['id']=$id;
                
                $r=$db->insert('smenuitems', $values);
                if(!$r){
                    $r=$db->update('smenuitems', $values, $where);
                }
                else{
                    $values['id']=$r;
                }
            }
            if(!$id&&$r){
                $db->update('smenuitems', array('position'=>$values['id']), array('id'=>$values['id']));
            }
            if($r){
                return '{"success":""}';
            }
            else{
                return '{"error":""}';
            }
        }
        elseif(isset($_POST['title'])&&isset($_POST['link'])){
            
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
                $db->delete('smenuitems', array('id'=>$id));
            }
        }
        else{
            $id=Data::number($_POST['id']);
            $db->delete('smenuitems', array('id'=>$id));
        }
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
            $db->update('smenuitems', array('position'=>$position), array('id'=>$id));
        }
    }
}
?>