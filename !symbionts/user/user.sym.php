<?
class SUser extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $user;
        if(!$template) $template='main';
        if($user->accessLevel){
            $this->signOut($template);
        }
        else{
            $this->signIn($template);
        }
    }
    public function signIn($template=null, $attributes=null, $content=null){
        global $user, $design;
        if($template) $template.='/signin';
        $template=$this->_check($template, 'main/signin');
        $vars=array('key'=>$user->key);
        $design->show($template, $vars);
    }
    public function signOut($template=null, $attributes=null, $content=null){
        global $user, $design, $db;
        if($template) $template.='/signout';
        $template=$this->_check($template, 'main/signout');
        $vars=$db->select('users', array('username', 'firstName', 'lastName'), array('id'=>$user->id), '', 1);
        $design->show($template, $vars);
    }
    
    public function signUp($template=''){
        global $user, $design;
        $vars=array('key'=>$user->key);
        if($template) $template.='/signup';
        $template=$this->_check($template, 'main/signup');
        $design->show($template, $vars);
    }
    public function dbSignUp($design=''){
        global $db, $user, $labels;
        if(!isset($_POST['username'])||!isset($_POST['password'])||!isset($_POST['email'])){
            print('{"error":"Not the all parametrs have been sent."}');
            return;
        }
        $username=data::word($_POST['username']);
        $password=data::word($_POST['password']);
        if(!data::isEmail($_POST['email'])){
            print('{"error":"'.$labels->get('symbionts.user.emailIncorrect').'"}');
            return;
        }
        $email=$_POST['email'];
        if($db->count('users', array('email'=>$email))){
            print('{"error":"'.$labels->get('symbionts.user.emailExists').'"}');
            return;
        }
        
        $firstName=isset($_POST['firstName'])?data::safe($_POST['firstName']):'';
        $lastName=isset($_POST['lastName'])?data::safe($_POST['lastName']):'';
        $city=isset($_POST['city'])?data::safe($_POST['city']):'';
        $country=isset($_POST['country'])?data::safe($_POST['country']):'';
        if($firstName&&$lastName&&$city&&$country&&$phone&&$address){
            $accessLevel=2;
        }
        else{
            $accessLevel=1;
        }
        $id=$db->insert('users', array('username'=>$username, 'password'=>$password, 'email'=>$email, 'firstName'=>$firstName, 'lastName'=>$lastName, 'city'=>$city, 'country'=>$country, 'accessLevel'=>$accessLevel));
        if($id){
            return '{"success":"'.$labels->get('symbionts.user.signedUp').'"}';
        }
        else{
            return '{"error":"'.$labels->get('symbionts.user.signUpError').'"}';
        }
    }
    public function check($design, $username=''){
        global $db;
        if(isset($_POST['username'])){
            $username=data::word($_POST['username'], 'en', 0, '-');
        }
        else{
            $username=data::word($username, 'en', 0, '-');
        }
        $count=$db->count('users', array('username'=>$username));
        if($count){
            $l=new label('symbionts.user.usernameError');
            return '{"error":"'.$l->get().'"}';
        }
        else{
            $l=new label('symbionts.user.usernameSuccess');
            return '{"ok":"'.$l->get().'"}';
        }
    }
    public function profile($design=''){
        global $db, $user;
        if($design) $design='profile/'.$design;
        $design=$this->_check($design, 'profile/edit');
        $d=new design($design, 'user', false);
        
        $u=$db->select('users', array('firstName', 'lastName', 'email', 'country', 'city', 'address', 'phone'), array('id'=>$user->id), '', 1);
        foreach($u as $key=>$val){
            $this->$key=$val;
        }
        $d->inc();
    }
    public function dbProfile($design=''){
        global $db, $user;
        
        $firstName=isset($_POST['firstName'])?data::safe($_POST['firstName']):'';
        $lastName=isset($_POST['lastName'])?data::safe($_POST['lastName']):'';
        $city=isset($_POST['city'])?data::safe($_POST['city']):'';
        $country=isset($_POST['country'])?data::safe($_POST['country']):'';
        $phone=isset($_POST['phone'])?data::safe($_POST['phone']):'';
        $address=isset($_POST['address'])?data::safe($_POST['address']):'';
        
        if($firstName&&$lastName&&$city&&$country&&$phone&&$address){
            $accessLevel=2;
        }
        else{
            $accessLevel=1;
        }
        
        $id=$db->update('users', array('firstName'=>$firstName, 'lastName'=>$lastName, 'city'=>$city, 'country'=>$country, 'phone'=>$phone, 'address'=>$address, 'accessLevel'=>$accessLevel), array('id'=>$user->id));
        if($id){
            $l=new label('symbionts.user.profile_saved');
            return '{"ok":"'.$l->get().'"}';
        }
        else{
            $l=new label('symbionts.user.profile_error');
            return '{"error":"'.$l->get().'"}';
        }
    }
    public function _edit($info=''){
        global $design;
        $vars=array();
        $vars['function']=$info->function;
        $design->show('symbionts/user/edit', $vars);
    }
    public function _info($info=''){
        global $db, $kernel, $labels;
        $title=$labels->get('symbionts.user');
        switch($info->function){
            case 'main': $title.=' → '.$labels->get('symbionts.user.signin'); break;
            case 'signUp': $title.=' → '.$labels->get('symbionts.user.signup'); break;
            case 'profile': $title.=' → '.$labels->get('symbionts.user.profile'); break;
        }
        return array('title'=>$title, 'block'=>false);
        
    }
}
?>