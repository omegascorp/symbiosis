<?
class SForms extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $design, $user, $db;
        $attributes=Data::extend(array(
            'uniq'=>''
        ), $attributes);
        $vars=array('email'=>'', 'name'=>'', 'uniq'=>$attributes['uniq']);
        $template=$this->_check("main/".$template, "main/main");
        if($user->id){
            $data=$db->select('users', array('email', 'firstName'), array('id'=>$user->id), '', 1);
            $vars['email']=$data['email'];
            $vars['name']=$data['firstName'];
        }
        $design->show($template, $vars);
    }
    public function send($template=null, $attributes=null, $content=null){
        global $labels, $kernel;
        $labels->import('db/labels/errors/');
        if(!isset($_POST['uniq'])||!isset($_POST['data'])){
            return '{"error":"'.$labels->get('errors.parametrs').'"}';
        }
        $uniq=Data::safe($_POST['uniq']);
        $file='db/forms.json';
        $json=json_decode(file_get_contents($file));
        if(!isset($json->$uniq)){
            return '{"error":"'.$labels->get('symbionts.forms.incorrectUniq').'"}';
        }
        $email=$json->$uniq;
        
        if($_SESSION['capcha_'.$_POST['captchaUniq']]!=$_POST['captcha']){
            return '{"error":"'.$labels->get('symbionts.forms.incorrectCaptcha').'"}';
        }
        
        $message='';
        foreach($_POST['data'] as $val){
            if(!isset($val['type'])) $val['type']='text';
            if(!isset($val['label'])) $val['label']='';
            switch($val['type']){
                case 'email':
                    if(!Data::isEmail($val['value'])){
                        return '{"error":"'.$labels->grab('symbionts.forms.incorrect', array('label'=>$val['label'])).'"}';
                    }
                    break;
                case 'text':
                    $val['value']=Data::htmlRemove($val['value']);
                    break;
            }
            $val['label']=Data::htmlRemove($val['label']);
            $message.=$val['label'].':'.$val['value'].'<br/>';
        }
        $kernel->addLibrary("Mail");
        
        $mail=new Mail($email, 'Robot', 'robot@'.$_SERVER["SERVER_NAME"], 'From the site', $message);
        if($mail->send()){
            return '{"success":"'.$labels->get('symbionts.forms.sent').'"}';   
        }
        else{
            return '{"error":"'.$labels->get('symbionts.forms.error').'"}';
        }
    }
    public function adminSave($template=null, $attributes=null, $content=null){
        if(!isset($_POST['uniq'])||!isset($_POST['emails'])){
            return;
        }
        $uniq=Data::safe($_POST['uniq']);
        $emails=Data::safe($_POST['emails']);
        
        $file='db/forms.json';
        $json=json_decode(file_get_contents($file));
        $json->$uniq=$emails;
        
        $f=fopen($file, 'w');
        fwrite($f, json_encode($json));
        fclose($f);
    }
    public function _add($info=null){
        global $db, $user;
        $uniq=Data::uniq(6);
        $file='db/forms.json';
        if(!file_exists($file)){
            $f=fopen($file, 'w');
            fwrite($f, "{}");
            fclose($f);
        }
        $json=json_decode(file_get_contents($file));
        while(isset($json->$uniq)){
            $uniq=Data::uniq(6);
        }
        $json->$uniq=$db->select('users', 'email', array('id'=>$user->id), '', 1);
        $f=fopen($file, 'w');
        fwrite($f, json_encode($json));
        fclose($f);
        return 'Forms.main.uniq='.$uniq;
    }
    public function _delete($info=null){
        $file='db/forms.json';
        $json=json_decode(file_get_contents($file));
        $uniq=$info->attributes["uniq"];
        unset($json->$uniq);
        $f=fopen($file, 'w');
        fwrite($f, json_encode($json));
        fclose($f);
        return true;
    }
    public function _admin($info=null){
        global $db, $design, $kernel;
        $info->attributes=Data::extend(array(
            'uniq'=>null
        ), $info->attributes);
        $vars=array();
        $vars['template']=$info->template?$info->template:'main';
        $vars['templates']=Data::read('!symbionts/forms/main/');
        foreach($vars['templates'] as $key=>$val){
            $vars['templates'][$key]=array(
                'title'=>substr($val,0,strlen($val)-9),
                'template'=>substr($val,0,strlen($val)-9),
            );
        }
        $file='db/forms.json';
        $json=json_decode(file_get_contents($file), true);
        $uniq=$info->attributes["uniq"];
        if($uniq==null){
            do{
                $uniq=Data::uniq(6);
            }while(isset($json[$uniq]));
            
        }
        $vars['uniq']=$uniq;
        if(isset($json[$uniq])) $vars['emails']=$json[$uniq];
        else $vars['emails']='';
        
        $design->show('symbionts/forms/_admin', $vars);
    }
}
?>