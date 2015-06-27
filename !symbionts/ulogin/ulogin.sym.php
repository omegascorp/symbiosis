<?
class SULogin extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $design;
        $template=$this->_check($template, 'main');
        $vars=array();
        $design->show($template, $vars);
    }
    public function auth($template=null, $attributes=null, $content=null){
        if(isset($_POST['token'])){
            global $user, $db;
            $url='http://ulogin.ru/token.php?token='.$_POST['token'].'&host='.$_SERVER['HTTP_HOST'];
            $s = @file_get_contents($url);
            if(!$s){
                if($curl = curl_init()){
                    curl_setopt($curl,CURLOPT_URL,$url);
                    curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
                    $s = curl_exec($curl);
                    curl_close($curl);
                }
            }
            $info = json_decode($s, true);
            if(isset($info['error'])) return;
            
            $user->username=$info['network'].$info['uid'];
            //$user->password=$info['access_token'];
            $user->firstName=$info['first_name'];
            $user->lastName=$info['last_name'];
            $user->accessLevel=1;
            $user->md5=false;
            $user->type=$info['network'];
            $user->token=Data::uniq(16);
            if($db->query("
                SELECT 1 FROM `users`
                WHERE LOWER(username)=LOWER('".$user->username."') AND isActive=1
            ", true)){
                $user->edit();
                $user->signIn();
            }
            else{
                $user->add();
                $user->signIn();
            }
        }
    }
}
?>