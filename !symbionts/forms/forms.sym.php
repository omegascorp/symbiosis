<?
class SForms extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $design, $user, $db;
        $attributes=Data::extend(array(
            'uniq'=>''
        ), $attributes);
        $vars=array('email'=>'', 'name'=>'', 'uniq'=>$attributes['uniq']);
        $template=$this->_check($template, "form-main");
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
        $message='';
        foreach($_POST['data'] as $val){
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
            $message.=$val['label'].' '.$val['value'].'<br/>';
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
    public function  _edit($info=null){
        global $db, $design, $kernel;
        $info->attributes=Data::extend(array(
            'uniq'=>''
        ), $info->attributes);
        if($info->template==null) $info->template="form-main";
        $vars=array();
        $vars['template']=substr($info->template, 5);
        $vars['templates']=Data::read('design/'.$kernel->conf->design.'/symbionts/forms/', '/form\-(.*)/');
        foreach($vars['templates'] as $key=>$val){
            $vars['templates'][$key]=array(
                'title'=>substr($val, 5, -9),
                'template'=>substr($val, 5, -9),
            );
        }
        
        $file='db/forms.json';
        $json=json_decode(file_get_contents($file));
        $vars['uniq']=$uniq=$info->attributes["uniq"];
        $vars['emails']=$json->$uniq;
        
        $design->show('symbionts/forms/edit', $vars);
    }
    public function editSave($template=null, $attributes=null, $content=null){
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
    public function _info($info=null){
        global $labels;
        $file='db/forms.json';
        $json=json_decode(file_get_contents($file));
        $uniq=$info->attributes["uniq"];
        $emails=$json->$uniq;
        return array('title'=>$labels->grab('symbionts.forms.sendingMessage', array('emails'=>$emails)), 'block'=>false);
    }
}
?>