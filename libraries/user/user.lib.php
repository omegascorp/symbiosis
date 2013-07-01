<?
class User{
    private $vars=array();
    private $id;
    private $key;
    private $md5=true;
    private $token;
    private $type;
    public function __construct($username=null, $password=null, $addation=null){
        if(is_array($addation)){
            foreach($addation as $key=>$val){
                $this->$key=$val;
            }
        }
        $this->username=$username;
        $this->password=$password;
        $this->vars['accessLevel']=0;
    }
    //Try to sign in
    public function autorisation(){
        global $kernel;
        $prefix=$kernel->conf->sessionPrefix;
        switch($kernel->conf->autorisationType){
            case 'cookies':
                $key=Data::uniq(7);
                if(!isset($_COOKIE[$prefix.'_key'])){
                    $this->key=$key;
                    setcookie($prefix.'_key', $key, 0, '/');
                }
                else{
                    $this->key=$_COOKIE[$prefix.'_key'];
                }
                if(isset($_COOKIE[$prefix.'_username'])){
                    $this->username=$_COOKIE[$prefix.'_username'];
                    $this->password=isset($_COOKIE[$prefix.'_password'])?$_COOKIE[$prefix.'_password']:'';
                    $this->md5=isset($_COOKIE[$prefix.'_md5'])?$_COOKIE[$prefix.'_md5']:false;
                    $this->type=isset($_COOKIE[$prefix.'_type'])?$_COOKIE[$prefix.'_type']:'';
                    $this->token=isset($_COOKIE[$prefix.'_type'])?$_COOKIE[$prefix.'_token']:'';
                }
                break;
            case 'session': default:
                if(!isset($_SESSION[$prefix.'_key'])) $_SESSION[$prefix.'_key']=Data::uniq(7);
                $this->key=$_SESSION[$prefix.'_key'];
                if(isset($_SESSION[$prefix.'_username'])&&isset($_SESSION[$prefix.'_password'])){
                    $this->username=$_SESSION[$prefix.'_username'];
                    $this->password=$_SESSION[$prefix.'_password'];
                    $this->md5=$_SESSION[$prefix.'_md5'];
                    $this->type=isset($_SESSION[$prefix.'_type'])?$_SESSION[$prefix.'_type']:'';
                    $this->token=isset($_SESSION[$prefix.'_token'])?$_SESSION[$prefix.'_token']:'';
                }
                break;
        }
        return $this->check();
    }
    //Is user exists
    public function check(){
        global $db;
        $this->username=Data::word($this->username);
        $this->password=Data::word($this->password);
        if($this->md5){
            $pass="MD5(CONCAT(u.password,'".$this->key."'))";
        }
        else{
            $pass='u.password';
        }
        if($this->token&&$this->type){
            $r=$db->query("
                SELECT u.accessLevel, u.id, u.timezone, u.username
                FROM `users` as u
                    LEFT JOIN `usersauth` as a
                        ON a.userId=u.id
                WHERE LOWER(u.username)=LOWER('".$this->username."') AND (".$pass."='".$this->password."' AND u.password!='' OR a.type='".$this->type."' AND a.token='".$this->token."') AND u.isActive=1
            ", true);
        }
        else{
            $r=$db->query("
                SELECT u.accessLevel, u.id, u.timezone, u.username
                FROM `users` as u
                WHERE LOWER(u.username)=LOWER('".$this->username."') AND ".$pass."='".$this->password."' AND u.password!='' AND u.isActive=1
            ", true);
        }
        if(is_array($r)){
            $this->vars['accessLevel']=$r['accessLevel'];
            $this->id=$r['id'];
            $this->username=$r['username'];
            mysql_query("SET time_zone = '".$r['timezone']."'");
        }
        return $this->accessLevel;
    }
    //Sign In
    public function signIn(){
        global $db, $kernel;
        if($this->check()){
            $prefix=$kernel->conf->sessionPrefix;
            switch($kernel->conf->autorisationType){
                case 'cookies':
                    setcookie($prefix.'_username', $this->username, 60 * 60 * 24 * 60 + time(), '/');
                    setcookie($prefix.'_password', $this->password, 60 * 60 * 24 * 60 + time(), '/');
                    setcookie($prefix.'_md5', $this->md5, 60 * 60 * 24 * 60 + time(), '/');
                    setcookie($prefix.'_type', $this->type, 60 * 60 * 24 * 60 + time(), '/');
                    setcookie($prefix.'_token', $this->token, 60 * 60 * 24 * 60 + time(), '/');
                    return true;
                case 'session': default:
                    $_SESSION[$prefix.'_username']=$this->username;
                    $_SESSION[$prefix.'_password']=$this->password;
                    $_SESSION[$prefix.'_md5']=$this->md5;
                    $_SESSION[$prefix.'_type']=$this->type;
                    $_SESSION[$prefix.'_token']=$this->token;
                    return true;
            }
        }
        return false;
    }
    //Sign out
    public function signOut(){
        global $kernel;
        $prefix=$kernel->conf->sessionPrefix;
        $this->username='';
        $this->password='';
        $this->type='';
        $this->token='';
        $this->accessLevel=0;
        switch($kernel->conf->autorisationType){
                case 'cookies':
                    setcookie($prefix.'_username', $this->username, 60 * 60 * 24 * 60 + time(), '/');
                    setcookie($prefix.'_password', $this->password, 60 * 60 * 24 * 60 + time(), '/');
                    setcookie($prefix.'_md5', $this->type, 60 * 60 * 24 * 60 + time(), '/');
                    setcookie($prefix.'_type', $this->type, 60 * 60 * 24 * 60 + time(), '/');
                    setcookie($prefix.'_token', $this->token, 60 * 60 * 24 * 60 + time(), '/');
                    return;
                case 'session': default:
                    unset($_SESSION[$prefix.'_username']);
                    unset($_SESSION[$prefix.'_password']);
                    unset($_SESSION[$prefix.'_md5']);
                    unset($_SESSION[$prefix.'_token']);
                    unset($_SESSION[$prefix.'_type']);
                    return;
        }
    }
    //User adding
    public function add(){
        global $db;
        $id=$db->insert('users', $this->vars);
        if($this->type&&$this->token){
            $db->insert('usersauth', array('type'=>$this->type, 'token'=>$this->token, 'userId'=>$id));
        }
        return $id;
    }
    //User locking
    public function lock(){
        global $db;
        return $db->update('users', array('isActive', 0), array('username'=>$this->username, 'password'=>$this->password), 1);
    }
    //User deleting
    public function delete(){
        global $db;
        return $db->delete('users', array('username'=>$this->username, 'password'=>$this->password), 1);
    }
    //User editing
    public function edit($id=0){
        global $db;
        
        if($id) $this->id=$id;
        else $this->id=$db->select('users', 'id', array('username'=>$this->username, 'password'=>$this->password), '', 1);
        
        $update=$db->update('users', $this->vars, array('id'=>$this->id), 1);
        if($this->type&&$this->token){
            $insert=$db->insert('usersauth', array('type'=>$this->type, 'token'=>$this->token, 'userId'=>$this->id));
            if(!$insert){
                $db->update('usersauth', array('type'=>$this->type, 'token'=>$this->token), array('userId'=>$this->id), 1);
            }
        }
        return $update;
    }
    public function __get($key){
        switch($key){
            case 'id': return $this->id;
            case 'key': return $this->key;
            case 'md5': return $this->md5;
            case 'token': return $this->token;
            case 'type': return $this->type;
            default: return isset($this->vars[$key])?$this->vars[$key]:'';
        }
    }
    public function __set($key, $val){
        switch($key){
            case 'id': $this->id=$val; break;
            case 'key': $this->key=$val; break;
            case 'md5': $this->md5=$val; break;
            case 'token': $this->token=$val; break;
            case 'type': $this->type=$val; break;
            default: $this->vars[$key]=$val;
        }
    }
}
?>