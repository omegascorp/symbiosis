<?
class SLanguages_Admin extends Symbiont{
    public function __construct(){
        parent::__construct('Languages');
    }
    public function main($template=null, $attributes=null, $content=null){
        global $db, $design, $user;
        if($user->accessLevel<9) return;
        $attributes=Data::extend(array(
            
        ),$attributes);
        $template=$this->_check($template, 'admin');
        //$languages=$db->select('languages', '*', '', 'position');
        $vars=array();
        $design->show($template, $vars);
    }
    public function items($template=null, $attributes=null, $content=null){
        global $db, $design, $user;
        if($user->accessLevel<9) return;
        $attributes=Data::extend(array(
            
        ),$attributes);
        $template=$this->_check($template, 'admin-languages');
        $languages=$db->select('languages', '*', '', 'position');
        $vars=array('languages'=>$languages);
        $design->show($template, $vars);
    }
    public function change($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        if($user->accessLevel<9){
            print($labels->get('errors.prerogatives'));
            return;
        }
        $vars=array(
            'id'=>'',
            'abbr'=>'',
            'title'=>'',
            'titleEn'=>'',
            'code'=>''
        );
        if(isset($_POST['id'])){
            $id=Data::number($_POST['id']);
            if($id!=0){
                $language=$db->select('languages', '*', array('id'=>$id), null, 1);
                if(!is_array($language)){
                    return 'The language is not exists.';
                }
                $vars=Data::extend($vars, $language);
            }
            $vars['id']=$id;
        }
        $design->show('symbionts/languages/change', $vars);
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
        $design->show('symbionts/languages/delete', $vars);
    }
    public function dbChange($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
       if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['abbr'])||!isset($_POST['title'])||!isset($_POST['titleEn'])||!isset($_POST['code'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        if(isset($_POST['id'])&&$_POST['id']){
            $where=array();
            $where['id']=Data::word($_POST['id']);
        }
        $values=array();
        $values['abbr']=Data::word($_POST['abbr']);
        $values['title']=Data::safe($_POST['title']);
        $values['titleEn']=Data::safe($_POST['titleEn']);
        $values['code']=Data::safe($_POST['code']);
        if($values['abbr']==''){
            print('{"error":"Abbreviation is empty"}');
            return;
        }
        if(isset($where)){
            $id=$db->update('languages', $values, $where, 1);
        }
        else{
            $id=$db->insert('languages', $values);
            $id=$db->update('languages', array('position'=>$id), array('id'=>$id));
        }
        
        if($id) print('{"success":"The language edited."}');
        else print('{"error":"The language didn\'t edited."}');
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
                $db->delete('languages', array('id'=>$id));
            }
        }
        else{
            $id=Data::number($_POST['id']);
            $db->delete('languages', array('id'=>$id));
        }
    }
    public function dbEnabled($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
       if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['id'])||!isset($_POST['enabled'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        $id=data::number($_POST['id']);
        $enabled=data::number($_POST['enabled']);
        $db->update('languages', array('isEnabled'=>$enabled), array('id'=>$id));
        print('{"success":'.($enabled==1?'"Lanuage is turned on"':'"Language is turned off"').'}');
    }
    public function dbDefault($template=null, $attributes=null, $content=null){
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
        $id=data::number($_POST['id']);
        $db->update('languages', array('isDefault'=>0));
        $db->update('languages', array('isDefault'=>1), array('id'=>$id));
        print('{"success":"Defautl language is changed."}');
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
            $db->update('languages', array('position'=>$position), array('id'=>$id), 1);
        }
    }
}
?>