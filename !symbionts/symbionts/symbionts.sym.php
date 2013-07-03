<?
//Symbionts 0.0.2
class SSymbionts extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db;
    }
    public function admin($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db, $user;
        if($user->accessLevel<9) return;
        $template=$this->_check($template, 'admin');
        
        $vars=array();
        $vars["symbionts"]=array();
        
        $symbiontsFile='temp/important/symbionts/symbionts.json';
        
        $symbionts=array();
        if(file_exists($symbiontsFile)){
            $symbionts=json_decode(file_get_contents($symbiontsFile));
        }
        
        $dir=opendir("!symbionts");
        while($file=readdir($dir)){
            if(($file!=".")&&($file!="..")&&($file[0]!='.')){
                $config='!symbionts/'.$file.'/config.json';
                $labels='!symbionts/'.$file.'/labels/'.$kernel->lang->abbr.'.json';
                $symbiont=array();
                $symbiont['name']=$file;
                $symbiont['title']=ucfirst($file);
                $symbiont['icon']=$symbiont['name'].'/icons/';
                $symbiont['admin']=false;
                $symbiont['version']='';
                $symbiont['description']='';
                $symbiont['active']=false;
                if(file_exists($config)){
                    $content=file_get_contents($config);
                    $json=json_decode($content);
                    
                    $symbiont['admin']=isset($json->admin)?$json->admin:false;
                    $symbiont['version']=isset($json->version)?$json->version:'';
                    if(file_exists($labels)){
                        $content=file_get_contents($labels);
                        $json=json_decode($content);
                        $index='symbionts.'.$file;
                        if(isset($json->$index)){
                            $symbiont['title']=$json->$index;
                        }
                        $index.='.description';
                        if(isset($json->$index)){
                            $symbiont['description']=$json->$index;
                        }
                    }
                    if($symbiont['admin']&&is_array($symbionts)){
                        if(in_array($file, $symbionts)){
                            $symbiont['active']=true;
                        }
                    }
                    array_push($vars["symbionts"], $symbiont);
                }
            }
        }
        closedir($dir);
        
        $design->show($template, $vars);
    }
    public function dbChange($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['name'])||!isset($_POST['remove'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        
        $name=Data::symbiont($_POST['name']);
        $remove=Data::number($_POST['remove']);
        
        $symbiontsFile='temp/important/symbionts/symbionts.json';
        
        $symbionts=array();
        if(file_exists($symbiontsFile)){
            $symbionts=json_decode(file_get_contents($symbiontsFile));
        }
        if(!is_array($symbionts)){
            $symbionts=array();
            if($remove){
                array_push($symbionts, $name);
            }
        }
        else{
            $insert=true;
            foreach($symbionts as $key=>$val){
                if($val==$name){
                    if(!$remove){
                        $symbionts=$this->arrayRemove($symbionts, $key);
                    }
                    $insert=false;
                    break;
                }
            }
            if($insert){
                array_push($symbionts, $name);
            }
        }
        print json_encode($symbionts);
        $f=fopen($symbiontsFile, 'w');
        fwrite($f, json_encode($symbionts));
        fclose($f);
        
        $positions_temp='temp/symbionts/admin.postions_'.$kernel->lang->abbr.'.json';
        if(file_exists($positions_temp)) unlink($positions_temp);
    }
    public function upload($template=null, $attributes=null, $content=null){
        global $kernel, $symbionts, $user;
        if($user->accessLevel<9) return;
        $kernel->addSymbiont('Filemanager');
        $file=json_decode($symbionts->Filemanager->upload($template, $attributes, $content));
        $kernel->addLibrary('Zip');
        $zip=new Zip($file->url);
        $pos=strrpos($file->name, '.');
        $name=substr($file->name, 0, $pos);
        $dir='';
        //Data::createFolder($dir);
        $zip->unzip($dir);
    }
    public function arrayRemove($array, $key){
        $count=count($array);
        for($i=$key; $i<$count-1; $i++){
            $array[$i]=$array[$i+1];
        }
        array_pop($array);
        print json_encode($array);
        return $array;
    }
}
?>