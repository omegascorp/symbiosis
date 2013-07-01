<?
class SFilemanager extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $design, $db, $kernel;
        $attributes=Data::extend(array(
            'buttons'=>false,
            'read'=>false
        ),$attributes);
        if(isset($_POST['path'])) $path=Data::filesystem($_POST['path']);
        else $path='/';
        if($template=='mobile') $attributes['read']=true;
        $template=$this->_check($template);
        $vars=array();
        $vars['path']=$path;
        $vars['buttons']=$attributes['buttons'];
        $vars['accessLevels']=$db->select('accesslevels', array('accessLevel', 'title'), array('languageId'=>$kernel->lang->id), 'accessLevel');
        if($attributes['read']){
            $read=$this->read(null, array('json'=>false));
            $vars['files']=$read["files"];
        }
        $design->show($template, $vars);
    }
    public function mini($template=null, $attributes=null, $content=null){
        global $design;
        $attributes=Data::extend(array(
            'path'=>'media'
        ),$attributes);
        $path=Data::filesystem($attributes['path']);
        if(substr($path, 0, 1)!='/') $path='/'.$path;
        if(substr($path, -1, 1)!='/') $path=$path.'/';
        $read=$this->read(null, array('json'=>false, 'path'=>$path));
        $vars=array();
        $vars['path']=$path;
        $vars['files']=$read["files"];
        $template=$this->_check($template, 'mini');
        $design->show($template, $vars);
    }
    public function read($template=null, $attributes=null, $content=null){
        global $kernel, $user;
        $attributes=Data::extend(array(
            'path'=>'/',
            'json'=>true,
            'hidden'=>false
        ),$attributes);
        
        if(isset($_POST['path'])) $path=Data::filesystem($_POST['path']);
        else $path=Data::filesystem($attributes['path']);
        
        
        if(isset($_POST['hidden'])) $hidden=Data::bool($_POST['hidden']);
        else $hidden=Data::bool($attributes['hidden']);
        
        $path=str_replace('..', '', $path);
        if(!isset($path[0])||$path[0]!='/') $path='/'.$path;
        $path='./!uploads'.$path;
        
        if(is_file($path)){
            $p=substr($path, 10, strlen($path)-11);
            $name=substr($p, strrpos($p, '/')+1);
            return '{"name":"'.$name.'"}';
        }
        
        if(file_exists($path.'.config')){
            $config=json_decode(file_get_contents($path.'.config'));
            if(isset($config->view)){
                if(isset($config->view->users)){
                    $user=false;
                    foreach($config->view->users as $id){
                        if($user->id==$id) $user=true;
                    }
                }
                $access=false;
                if(isset($config->view->accessLevel)&&$user->accessLevel>=$config->view->accessLevel) $access=true;
                if(!$user&&!$access){
                    $p=substr($path, 10, strlen($path)-11);
                    $name=substr($p, strrpos($p, '/')+1);
                    return '{"error":"Access is denied","errorCode":1,"name":"'.$name.'"}';
                }
            }
        }
        else{
            $p=substr($path, 10, strlen($path)-11);
            $name=substr($p, strrpos($p, '/')+1);
            return '{"error":"Config is not exsists","errorCode":0,"name":"'.$name.'"}';
        }
        
        $vars=array();
        $vars['files']=array();
        $dir=opendir($path);
        while($file=readdir($dir)){
            if($file!='.'&&$file!='..'&&($file[0]!='.'||$hidden)){
                $current=array();
                $current['name']=$file;
                if(is_dir($path.$file)){
                    $current['extension']='';
                    $current['type']='folder';
                    if(file_exists($path.$file.'/.config')){
                        $current['config']=json_decode(file_get_contents($path.$file.'/.config'));
                    }
                }
                else{
                    $current['extension']=Data::getFileType($file);
                    if(!file_exists($this->icon('128', $current['extension']))){
                        $current['type']='file';
                    }
                }
                if($current['extension']=='jpg'||$current['extension']=='gif'||$current['extension']=='png'||$current['extension']=='bmp'){
                    $current['type']='image';
                    $current['icon']=$path.'.128/'.$file;
                }
                else if($current['extension']=='mp3'){
                    $current['type']='sound';
                }
                else{
                    $current['icon']=$this->icon('128', $current['extension']);
                }
                $current['date']=filemtime($path.$file);
                array_push($vars['files'], $current);
            }
        }
        $vars['config']=$config;
        $p=substr($path, 10, strlen($path)-11);
        $vars['name']=substr($p, strrpos($p, '/')+1);
        if($attributes['json']) $vars=json_encode($vars);
        return $vars;
    }
    public function createFolder($template=null, $attributes=null, $content=null){
        global $kernel, $user;
        if(isset($_POST['path'])) $path=Data::filesystem($_POST['path']);
        else $path='/';
        
        if(isset($_POST['name'])) $name=Data::filesystem($_POST['name']);
        else return;
        
        $path=str_replace('..', '', $path);
        if(!isset($path[0])||$path[0]!='/') $path='/'.$path;
        $path='!uploads'.$path;
        
        if(!$this->access($path)){
            return '{"error":"Access is denied"}';
        }
        
        $folder=Data::createFolder($path.$name);
        if($folder){
            $file=fopen($path.$name.'/.config', 'w');
            $config=json_encode(array(
                'view'=>array(
                    'accessLevel'=>0
                ),
                'change'=>array(
                    'accessLevel'=>1
                ),
                'maxsize'=>'2 MB',
                'types'=>array('#images')
            ));
            fwrite($file, $config);
            fclose($file);
        }
        if($folder) return '{"success":""}';
        else return '{"error":""}';
    }
    public function delete($template=null, $attributes=null, $content=null){
        global $kernel, $user;
        if(isset($_POST['path'])) $path=Data::filesystem($_POST['path']);
        else $path='/';
        $path=str_replace('..', '', $path);
        if(!isset($path[0])||$path[0]!='/') $path='/'.$path;
        $path='!uploads'.$path;
        if(!isset($_POST['files'])) return '{"error": ""}';
        if(is_array($_POST['files'])){
            foreach($_POST['files'] as $file){
                $file=Data::safe($file);
                if($this->access($path.$file)){
                    Data::delete($path.$file);
                    $type=Data::getFileType($file);
                    if($type=='png'||$type=='jpg'||$type=='jpeg'||$type=='gif'||$type=='bmp'){
                        Data::delete($path.'.64/'.$file);
                        Data::delete($path.'.128/'.$file);
                    }
                }
            }
        }
        return '{"success": ""}';
    }
    private function icon($size, $type){
        global $kernel;
        return '!symbionts/filemanager/filetypes/big/'.$type.'.png';
    }
    public function access($file){
        global $user;
        if(is_file($file)){
            $pos=strrpos($file, '/');
            $file=substr($file, 0, $pos+1);
        }
        else{
            if($file[strlen($file)-1]!='/'){
                $file.='/';
            }
        }
        if(file_exists($file.'.config')){
            $config=json_decode(file_get_contents($file.'.config'));
            if(isset($config->view)){
                if(isset($config->view->users)){
                    $user=false;
                    foreach($config->view->users as $id){
                        if($user->id==$id) $user=true;
                    }
                }
                $access=false;
                if(isset($config->view->accessLevel)&&$user->accessLevel>=$config->view->accessLevel) $access=true;
                if(!$user&&!$access){
                    return 0;
                }
            }
        }
        else{
            return 0;
        }
        return 1;
    }
    public function fileExists($template=null, $attributes=null, $content=null){
        $path=Data::fileSystem($_POST['path']);
        $path=str_replace('..', '', $path);
        if(!isset($path[0])||$path[0]!='/') $path='/'.$path;
        $path='!uploads'.$path;
        
        if(file_exists($path)){
            $exists='true';
        }
        else{
            $exists='false';
        }
        return '{"exists": '.$exists.'}';
    }
    public function fileConfig($template=null, $attributes=null, $content=null){
        $path=Data::fileSystem($_POST['path']);
        $path=str_replace('..', '', $path);
        while(substr($path, strlen($path)-1)=='/'){
            $path=substr($path, 0, strlen($path)-1);
        }
        $folder=substr($path, strrpos($path, '/')+1);
        $path=substr($path, 0, strrpos($path, '/'));
        $path.='/';
        if(!isset($path[0])||$path[0]!='/') $path='/'.$path;
        $path='!uploads'.$path;
        
        $config=array();
        $config['view']=array();
        $config['view']['accessLevel']=Data::number($_POST['view']);
        $config['change']=array();
        $config['change']['accessLevel']=Data::number($_POST['change']);
        $config['maxsize']=Data::safe($_POST['maxsize']);
        $types=Data::safe($_POST['types']);
        $exp=explode(',', $types);
        $config['types']=array();
        foreach($exp as $val){
            $val=trim($val);
            if($val=='') continue;
            array_push($config['types'], $val);
        }
        $name=isset($_POST['name'])?Data::safe($_POST['name']):'';
        
        $file=fopen($path.$folder.'/.config', 'w');
        fwrite($file, json_encode($config));
        fclose($file);
        if($name&&$name!=$folder){
            if(!file_exists($path.$name)){
                rename($path.$folder, $path.$name);
                return '{"success":""}';
            }
            else{
                return '{"error":""}';
            }
        }
        return '{"success":""}';
    }
    public function fileRename($template=null, $attributes=null, $content=null){
        $path=Data::fileSystem($_POST['path']);
        $path=str_replace('..', '', $path);
        if(!isset($path[0])||$path[0]!='/') $path='/'.$path.'/';
        $path='!uploads'.$path;
        
        $name=Data::safe($_POST['name']);
        $nameNew=isset($_POST['nameNew'])?Data::safe($_POST['nameNew']):'';
        
        if($nameNew){
            if(!file_exists($nameNew)){
                rename($path.$name, $path.$nameNew);
                return '{"success":""}';
            }
            else{
                return '{"error":""}';
            }
        }
        return '{"success":""}';
    }
    public function fileRead($template=null, $attributes=null, $content=null){
        global $design;
        if(!isset($_POST['path'])) return;
        $path='!uploads'.Data::fileSystem($_POST['path']);
        $type=Data::getFileType($path);
        
        switch($type){
            case 'txt': case 'config':
                $template=$this->_check($template, 'file-text');
                $vars=array();
                $vars['text']='';
                if(file_exists($path)) $vars['text']=file_get_contents($path);
                $design->show($template, $vars);
            break;
            default:
                
        }
    }
    public function upload($template=null, $attributes=null, $content=null){
        global $kernel, $userá, $labels;
        $eid=0;
        $errors=array(
            $labels->get('symbionts.filemanager.error0'),
            $labels->get('symbionts.filemanager.error1'),
            $labels->get('symbionts.filemanager.error2'),
            $labels->get('symbionts.filemanager.error3'),
            $labels->get('symbionts.filemanager.error4'),
            $labels->get('symbionts.filemanager.error5'),
            $labels->get('symbionts.filemanager.error6'),
            $labels->get('symbionts.filemanager.error7'),
            $labels->get('symbionts.filemanager.error8'),
            $labels->get('symbionts.filemanager.error9'),
            $labels->get('symbionts.filemanager.error10'),
            $labels->get('symbionts.filemanager.error11'),
            $labels->get('symbionts.filemanager.error12'),
            $labels->get('symbionts.filemanager.error13'),
            $labels->get('symbionts.filemanager.error14')
        );
        $input='';
        if(isset($_POST['input'])) $input=Data::word($_POST['input']);
        else if(isset($_GET['input'])) $input=Data::word($_GET['input']);
        
        $path='';
        if(isset($_POST['path'])) $path=Data::fileSystem($_POST['path']);
        else if(isset($_GET['path'])) $path=Data::fileSystem($_GET['path']);
        
        $overwrite=true;
        if(isset($_POST['overwrite'])) $overwrite=Data::bool($_POST['overwrite']);
        else if(isset($_GET['overwrite'])) $overwrite=Data::bool($_GET['overwrite']);
        
        $name=Data::fileName($_FILES[$input]['name']);
        $size=@filesize($_FILES[$input]['tmp_name']);
        $type=Data::getFileType($name);
        
        
        $path=str_replace('..', '', $path);
        if(!isset($path[0])||$path[0]!='/') $path='/'.$path;
        $path='!uploads'.$path;
        
        $nameNew='';
        if(isset($_POST['name'])) $nameNew=Data::fileName($_POST['name']);
        if(isset($_GET['name'])) $nameNew=Data::fileName($_GET['name']);
        if(!$nameNew) $nameNew=$_FILES[$input]['name'];
        if(!$overwrite) $nameNew=Data::fileFree($nameNew, $path, true);
        
        $return='json';
        if(isset($_POST['return'])) $return=Data::safe($_POST['return']);
        if(isset($_GET['return'])) $return=Data::safe($_GET['return']);
        
        if(isset($_FILES[$input]['error'])&&$_FILES[$input]['error']!=0){
            $eid=$_FILES[$input]['error'];
            if($eid>9) $eid=9;
        }
        elseif($name===''){
            $eid=4;
        }
        elseif(file_exists($path.'.config')){
	    $config=json_decode(file_get_contents($path.'.config'));
            if(isset($config->change)){
                $user=true;
                $access=true;
                if(isset($config->change->users)){
                    $user=false;
                    foreach($config->change->users as $id){
                            if($user->id==$id) $user=true;
                    }
                }
                if(isset($config->change->access_level)&&$config->change->access_level<$user->access_level) $access=true;
                if(!$user&&!$access){
                    $eid=11;
                }
            }
            if(isset($config->types)){
                $isCorrect=false;
                foreach($config->types as $t){
                    if(substr($t,0,1)=="#"){
                        if($t=='#images'){
                            if($type=='jpg'||$type=='jpeg'||$type=='png'||$type=='gif'||$type=='bmp'||$type=='tif'){
                                $isCorrect=true;
                                break;
                            }
                        }
                        if($t=='#video'){
                            if($type=='flv'||$type=='mp4'||$type=='avi'||$type=='3gp'){
                                $isCorrect=true;
                                break;
                            }
                        }
                        if($t=='#archives'){
                            if($type=='zip'||$type='rar'||$type=='7z'||$type=='bz'||$type=='gz'){
                                $isCorrect=true;
                                break;
                            }
                        }
                        if($t=='#sounds'){
                            if($type=='mp3'){
                                $isCorrect=true;
                                break;
                            }
                        }
                        if($t=='#documents'){
                            if($type=='txt'
                               ||$type='doc'||$type=='docx'||$type=='docm'||$type=='dot'||$type=='dotx'||$type=='dotm'||$type=='odt'
                               ||$type=='xls'||$type=='xlsx'||$type=='xlsm'||$type=='xlt'||$type=='xltx'||$type=='xltm'||$type=='xlsb'||$type=='xlam'||$type=='ods'
                               ||$type=='ppt'||$type=='pptx'||$type=='pptm'||$type=='pptx'||$type=='potm'||$type=='ppam'||$type=='ppsx'||$type=='ppsm'||$type=='sldx'||$type=='sldm'||$type=='thmx'||$type=='odp'){
                                $isCorrect=true;
                                break;
                            }
                        }
                        
                    }
                    else{
                        if($t==$type){
                            $isCorrect=true;
                            break;
                        }
                    }
                }
                if(!$isCorrect) $eid=14;
            }
            else{
                $eid=14;
            }
            if(isset($config->maxsize)){
                $maxsize=strtok($config->maxsize, ' ');
                $unit=strtolower(strtok(' '));
                $ñoef=1;
                switch($unit){
                    case 'b': $ñoef=1; break;
                    case 'kib': $ñoef=1024; break;
                    case 'kb': $ñoef=1000; break;
                    case 'mib': $ñoef=1048576; break;
                    case 'mb': $ñoef=1000000; break;
                    case 'gib': $ñoef=1073741824; break;
                    case 'gb': $ñoef=1000000000; break;
                    default: $ñoef=1048576;
                }
                if($size>($maxsize*$ñoef)){
                    $eid=13;
                }
            }
            $fileName=$path.$nameNew;
            
            if($eid==0){
                if(isset($_FILES[$input]['tmp_name'])&&$_FILES[$input]['tmp_name']!='none'){
                    if(!@move_uploaded_file($_FILES[$input]['tmp_name'], $fileName)){
                        $eid=9;
                    }
                }
                else{
                    $eid=4;
                }
	    }
            
            $icon32='';
            $icon64='';
            $icon128='';
            if($eid==0&&($type=='png'||$type=='jpg'||$type=='jpeg'||$type=='gif'||$type=='bmp')){
                $kernel->addLibrary('Image');
                $image=new Image($path.$nameNew);
                $icon32=$image->resize('/.32/*', 32, 32, true, true);
                $icon64=$image->resize('/.64/*', 64, 64, true, true);
                $icon128=$image->resize('/.128/*', 128, 128, true, true);
            }
            
            if($return=="json"){
                $ret= '{'."\n";
                $ret.= '"errorId": "'.$eid.'",'."\n";
                $ret.= '"error": "'.$errors[$eid].'",'."\n";
                $ret.= '"name": "'.$nameNew.'",'."\n";
                $ret.= '"size": "'.$size.'",'."\n";
                $ret.= '"url": "'.$path.$nameNew.'",'."\n";
                $ret.= '"icon32": "'.$icon32.'",'."\n";
                $ret.= '"icon64": "'.$icon64.'",'."\n";
                $ret.= '"icon128": "'.$icon128.'"'."\n";
                $ret.= '}'."\n";
                return $ret;
            }
            else if($return=="html"){
                return '<img src="!uploads/redactor/'.$nameNew.'" />';
            }
        }
        else{
            
        }
    }
}
?>