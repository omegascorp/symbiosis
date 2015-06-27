<?
class SMenu_Admin extends Symbiont{
    public function __construct(){
        parent::__construct('Menu');
    }
    public function main($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db, $symbionts;
        if($kernel->link->param2){
            $kernel->addSymbiont('Menu-AdminItems');
            $symbionts->MenuAdminItems->main($template=null, $attributes=null, $content=null);
        }
        else{
            $template=$this->_check($template, 'admin');
            $design->show($template);
        }
    }
    public function items($template=null, $parentId=0){
        global $kernel, $design, $db, $symbionts;
        $menus=$db->select('smenu', '*', array('languageId'=>$kernel->lang->id), 'position');
        $vars=array('menus'=>$menus);
        $template=$this->_check($template, 'admin-menus');
        $design->show($template, $vars);
    }
    public function change($template=null, $attributes=null, $content=null){
        $template=$this->_check($template, 'change');
        global $design, $db, $kernel;
        $id=isset($_POST['id'])?Data::number($_POST['id']):0;
        if($id){
            $vars=$db->select('smenu', array('id', 'alias', 'title'), array('id'=>$id, 'languageId'=>$kernel->lang->id), '', 1);
            $vars['languages']=$db->query('
                SELECT
                    l.id,
                    l.abbr,
                    l.isDefault,
                    l.title as languageTitle,
                    m.alias,
                    m.title
                FROM `languages` as l
                    LEFT JOIN `smenu` as m ON m.id='.$id.' AND m.languageId=l.id
                WHERE l.isEnabled=1
                ORDER BY l.position
            ');
        }
        else{
            $vars=array(
                'id'=>$id,
                'title'=>'',
                'alias'=>''
            );
            $vars['languages']=$db->query('
                SELECT
                    l.id,
                    l.abbr,
                    l.isDefault,
                    l.title AS languageTitle,
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
        global $kernel, $db;
        $alias=isset($_POST['alias'])?Data::safe($_POST['alias']):'';
        $id=isset($_POST['id'])?Data::number($_POST['id']):'';
        $values=array();
        $where=array();
        if(isset($_POST['languages'])){
            foreach($_POST['languages'] as $key=>$language){
                $where['languageId']=Data::number($key);
                $where['id']=$id;
                
                $values['title']=Data::safe($language['title']);
                $values['alias']=isset($language['alias'])?Data::safe($language['alias']):$alias;
                $values['languageId']=$where['languageId'];
                if($id) $values['id']=$id;
                
                if($values['alias']==='') continue;
                $r=$db->insert('smenu', $values);
                if(!$r){
                    $db->update('smenu', $values, $where);
                }
                else{
                    $values['id']=$r;
                }
            }
            if(!$id){
                $db->update('smenu', array('position'=>$values['id']), array('id'=>$values['id']));
            }
            return '{"success":""}';
        }
        elseif(isset($_POST['title'])&&isset($_POST['alias'])){
            $where['languageId']=$kernel->lang->id;
            $where['id']=$id;
            
            $values['title']=Data::safe($_POST['title']);
            $values['alias']=Data::safe($_POST['alias']);
            $values['languageId']=$kernel->lang->id;
            
            $r=$db->insert('smenu', $values);
            if(!$r){
                $db->update('smenu', $values, $where);
            }
            else{
                $db->update('smenu', array('position'=>$r), array('id'=>$r));
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
                $db->delete('smenu', array('id'=>$id));
            }
        }
        else{
            $id=Data::number($_POST['id']);
            $db->delete('smenu', array('id'=>$id));
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
            $db->update('smenu', array('position'=>$position), array('id'=>$id));
        }
    }
}
?>