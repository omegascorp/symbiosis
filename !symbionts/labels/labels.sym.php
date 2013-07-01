<?
//Labels 0.0.1
class SLabels extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $db, $design, $kernel;
        
        $vars=array();
        $template=$this->_check($template, 'admin');
        
        $main=Data::read('db/labels/');
        $vars['main']=array();
        foreach($main as $file){
            $name='db/labels/'.$file.'/'.$kernel->lang->abbr.'.json';
            if(!file_exists($name)) continue;
            $json=json_decode(file_get_contents($name));
            $arr=get_object_vars($json);
            array_push($vars['main'], array(
                'title'=>array_shift($arr),
                'alias'=>$file,
                'path'=>'db/labels/'.$file.'/'.$kernel->lang->abbr
            ));
        }
        
        $symbionts=Data::read('!symbionts/');
        $vars['symbionts']=array();
        foreach($symbionts as $file){
            $name='!symbionts/'.$file.'/labels/'.$kernel->lang->abbr.'.json';
            if(!file_exists($name)) continue;
            $json=json_decode(file_get_contents($name));
            $arr=get_object_vars($json);
            array_push($vars['symbionts'], array(
                'title'=>array_shift($arr),
                'alias'=>$file,
                'path'=>'!symbionts/'.$file.'/labels/'.$kernel->lang->abbr
            ));
        }
        
        $design->show($template, $vars);
    }
    public function change($template=null, $attributes=null, $content=null){
        $template=$this->_check($template, 'change');
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            return $labels->get('errors.prerogatives');
        }
        if(!isset($_POST['path'])){
            return $labels->get('errors.parametrs');
        }
        
        $path=Data::safe($_POST['path']);
        $vars=array('path'=>$path);
        $file=$path.'.json';
        if(!file_exists($file)){
            return;
        }
        $json=json_decode(file_get_contents($file));
        $vars['labels']=get_object_vars($json);
        $vars['title']='';
        foreach($vars['labels'] as $val){
            $vars['title']=$val;
            break;
        }
        $design->show($template, $vars);
    }
    public function dbChange($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            return '{"error":"'.$labels->get('errors.prerogatives').'"}';
        }
        if(!isset($_POST['path'])||!isset($_POST['labels'])){
            return '{"error":"'.$labels->get('errors.parametrs').'"}';
        }
        
        $path=Data::fileSystem($_POST['path']);
        $file=$path.'.json';
        if(!file_exists($file)){
            return '{"error":"'.$labels->get('errors.fileNotExists').'"}';
        }
        
        $labels=json_decode(file_get_contents($file));
        $f=fopen($file, 'w');
        foreach($_POST['labels'] as $key=>$val){
            $labels->$key=$val;
        }
        fwrite($f, json_encode($labels));
        fclose($f);
        
        return '{"success":""}';
    }
}
?>