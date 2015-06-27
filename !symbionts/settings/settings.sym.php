<?
class SSettings extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $design, $kernel, $db, $user;
        if($user->accessLevel<9) return;
        $template=$this->_check($template, 'main');
        $vars=array();
        $vars["languages"]=$db->select('languages', array('title', 'abbr'), '', 'position');
        $design->show($template, $vars);
    }
    public function dbSave($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['settings'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        
        $config=json_decode(file_get_contents('db/config.json'));
        
        $settings=$_POST['settings'];
        foreach($settings as $key=>$setting){
            $key=Data::safe($key);
            $setting=Data::isBool($setting)?Data::bool($setting):Data::safe($setting);
            $config->$key=$setting;
            
        }
        $file=fopen('db/config.json', 'w');
        fwrite($file, json_encode($config));
        fclose($file);
    }
}
?>