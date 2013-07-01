<?
//Users 0.0.2
class SUsers extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        
    }
    public function admin($template=null, $attributes=null, $content=null){
        global $kernel, $user, $db, $design;
        if($user->accessLevel<9) return;
        
        $vars=array();
        //$vars['users']=$db->select('users', array('username', 'password', 'accessLevel', 'isActive', 'firstName', 'lastName', 'email'), '', 'id');
        $vars['users']=$db->query('
            SELECT u.id, u.username, u.accessLevel, u.isActive, u.firstName, u.lastName, u.email, u.date, g.title
            FROM `users` as u
                LEFT JOIN `accesslevels` as g
                    ON g.accesslevel = u.accessLevel AND g.languageId='.$kernel->lang->id.'
        ');
        
        $template=$this->_check($template, 'admin');
        
        $design->show($template, $vars);
    }
    public function items($template=null, $attributes=null, $content=null){
        global $kernel, $user, $db, $design;
        
        if($user->accessLevel<9) return;
        
        $vars=array();
        $vars['users']=$db->query('
            SELECT u.id, u.username, u.accessLevel, u.isActive, u.firstName, u.lastName, u.email, u.date, g.title
            FROM `users` as u
                LEFT JOIN `accesslevels` as g
                    ON g.accesslevel = u.accessLevel AND g.languageId='.$kernel->lang->id.'
        ');
        
        $template=$this->_check($template, 'admin-items');
        $design->show($template, $vars);
    }
    public function change($template=null, $attributes=null, $content=null){
        global $design, $db, $kernel, $user;
        
        if($user->accessLevel<9) return;
        
        $id=isset($_POST['id'])?Data::number($_POST['id']):0;
        if($id){
            $vars=$db->select('users', array('id', 'username', 'firstName', 'lastName', 'email', 'country', 'city', 'sex', 'timezone', 'accessLevel'), array('id'=>$id), '', 1);
            $vars['accessLevels']=$db->select('accesslevels', array('title', 'accessLevel'), 'accessLevel>0 AND languageId='.$kernel->lang->id, 'accessLevel');
        }
        else{
            $vars=array(
                'id'=>$id,
                'username'=>'',
                'firstName'=>'',
                'lastName'=>'',
                'email'=>'',
                'country'=>'',
                'city'=>'',
                'sex'=>'1',
                'timezone'=>$db->select('users', 'timezone', array('id'=>$user->id), '', 1),
                'accessLevel'=>1
            );
            $vars['accessLevels']=$db->select('accesslevels', array('title', 'accessLevel'), 'accessLevel>0 AND languageId='.$kernel->lang->id, 'accessLevel');
        }
        $template=$this->_check($template, 'change');
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
        $design->show('symbionts/users/delete', $vars);
    }
    public function dbChange($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        if($user->accessLevel<9){
            print($labels->get('errors.prerogatives'));
            return;
        }
        if(!isset($_POST['username'])||!isset($_POST['firstName'])||!isset($_POST['lastName'])||!isset($_POST['email'])||!isset($_POST['country'])||!isset($_POST['city'])||!isset($_POST['sex'])||!isset($_POST['timezone'])){
            print($labels->get('errors.parametrs'));
            return;
        }
        
        
        $values=array();
        $values['username']=Data::safe($_POST['username']);
        if(isset($_POST['password'])) $values['password']=Data::safe($_POST['password']);
        $values['firstName']=Data::safe($_POST['firstName']);
        $values['lastName']=Data::safe($_POST['lastName']);
        $values['email']=Data::safe($_POST['email']);
        $values['country']=Data::safe($_POST['country']);
        $values['city']=Data::safe($_POST['city']);
        $values['sex']=Data::safe($_POST['sex']);
        $values['timezone']=Data::safe($_POST['timezone']);
        $values['accessLevel']=Data::number($_POST['accessLevel']);
        
        if(!isset($_POST['id'])||!$_POST['id']){
            $r=$db->insert('users', $values); 
        }
        else{
            $id=Data::number($_POST['id']);
            $r=$db->update('users', $values, array('id'=>$id));
        }
        if($r) return '{"success":""}';
        else return '{"error":""}';
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
                $db->delete('users', array('id'=>$id));
            }
        }
        else{
            $id=Data::number($_POST['id']);
            $db->delete('users', array('id'=>$id));
        }
        print('{"success":""}');
    }
}
?>