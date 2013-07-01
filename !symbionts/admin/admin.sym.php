<?
//Admin 0.0.12
class SAdmin extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $kernel, $symbionts, $design, $labels, $user;
        if($user->accessLevel<9) return;
        if($kernel->link->param1!=''){
            $symbiont=ucfirst($kernel->link->param1);
            $kernel->addSymbiont($symbiont);
            if($kernel->isExistsSymbiont($symbiont)){
                $symbionts->$symbiont->admin();
            }
            else{
                $design->show('symbionts/admin/error', array('error'=>$labels->grab('symbionts.admin.notinstalled', array('symbiont'=>$symbiont))));
            }
        }
        else{
            $kernel->addSymbiont('Main-Admin');
            $symbionts->MainAdmin->main();
        }
    }
    public function top($template=null, $attributes=null, $content=null){
        global $design;
        $design->show('symbionts/admin/top');
    }
    public function error($template=null, $attributes=null, $content=null){
        $attributes=Data::extend(array(
            'label'=>'',
            'vars'=>array()
        ),$attributes);
        global $design, $labels;
        $design->show('symbionts/admin/error', array('error'=>$labels->grab($attributes['label'], $attributes['vars'])));
    }
    public function symbionts($template=null, $attributes=null, $content=null){
        global $design, $kernel;
        
        $template=$this->_check($template, 'symbionts');
        
        $vars=array();
        $vars["symbionts"]=array();
        
        Data::createFolder('temp/important/symbionts/');
        Data::createFolder('temp/symbionts/');
        $positions_temp='temp/symbionts/admin.postions_'.$kernel->lang->abbr.'.json';
        $positions='temp/important/symbionts/admin.positions_'.$kernel->lang->abbr.'.json';
        $symbiontsFile='temp/important/symbionts/symbionts.json';
        
        if(!file_exists($positions_temp)){
            $symbionts=array();
            if(file_exists($symbiontsFile)){
                $symbionts=json_decode(file_get_contents($symbiontsFile));
            }
            if(is_array($symbionts)){
                foreach($symbionts as $file){
                    
                    $config='!symbionts/'.$file.'/config.json';
                    $labels='!symbionts/'.$file.'/labels/'.$kernel->lang->abbr.'.json';
                    $symbiont=array();
                    $symbiont['name']=$file;
                    $symbiont['title']=ucfirst($file);
                    $symbiont['icon']=$symbiont['name'].'/icons/';
                    if(file_exists($config)){
                        $content=file_get_contents($config);
                        $json=json_decode($content);
                        if(isset($json->admin)&&$json->admin){
                            
                        }
                        else{
                            continue;
                        }
                        
                        if(file_exists($labels)){
                            $content=file_get_contents($labels);
                            $json=json_decode($content);
                            $index='symbionts.'.$file;
                            if(isset($json->$index)){
                                $symbiont['title']=$json->$index;
                            }
                        }
                        
                        array_push($vars["symbionts"], $symbiont);
                    }
                }
            }
            
            
            $writer=fopen($positions, "w");
            fwrite($writer, json_encode($vars["symbionts"]));
            fclose($writer);
            
            $writer=fopen($positions_temp, "w");
            fclose($writer);
            
        }
        elseif(file_exists($positions)){
            $content=file_get_contents($positions);
            $json=json_decode($content);
            $folder='!symbionts/';
            foreach($json as $s){
                $symbiont=array();
                $symbiont['name']=$s->name;
                $symbiont['title']=$s->title;
                $symbiont['icon']=$symbiont['name'].'/icons/';
                array_push($vars['symbionts'], $symbiont);
            }
        }
        
        $config_file='temp/important/symbionts/admin.config.json';
        if(file_exists($config_file)){
            $config=json_decode(file_get_contents($config_file));
            $vars['isHidden']=$config->isHidden;
        }
        else{
            $vars['isHidden']='false';
        }
        
        $design->show($template, $vars);
    }
    public function dbSort($template=null, $attributes=null, $content=null){
        global $user, $kernel, $labels;
        $labels->import("db/labels/errors");
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['sort'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        $positions='temp/important/symbionts/symbionts.json';
        $sort=$_POST['sort'];
        $writer=fopen($positions, "w");
        fwrite($writer, json_encode($sort));
        fclose($writer);
        
        $positions_temp='temp/symbionts/admin.postions_'.$kernel->lang->abbr.'.json';
        if(file_exists($positions_temp)) unlink($positions_temp);
    }
    public function dbHide($template=null, $attributes=null, $content=null){
        global $user, $labels;
        $labels->import("db/labels/errors");
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['isHidden'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        $vars=array('isHidden'=>Data::word($_POST['isHidden']));
        $file='temp/important/symbionts/admin.config.json';
        $writer=fopen($file, "w");
        fwrite($writer, json_encode($vars));
        fclose($writer);
    }
    public function widgets($template=null, $attributes=null, $content=null){
        global $design;
        $template=$this->_check($template, 'widgets');
        $design->show($template);
    }
    public function widgetHome($template=null, $attributes=null, $content=null){
        global $design;
        $template=$this->_check($template, 'widgetHome');
        $design->show($template);
    }
    public function widgetClean($template=null, $attributes=null, $content=null){
        global $design;
        $template=$this->_check($template, 'widgetClean');
        $design->show($template);
    }
    public function _edit($info=null){
        global $db, $design, $kernel;
        if($info->function=='main'){
            $alias=Data::safe($kernel->link->param2);
            $main=$db->select('pages', array('symbiont', 'id'), array('alias'=>$alias), '', 1);
            $vars=array('isMain'=>($main['symbiont']=='Admin'), 'id'=>$main['id']);
            $design->show('symbionts/admin/edit', $vars);
        }
    }
    public function _admin($info=null){
        global $design;
        print $design->show('symbionts/admin/_admin');
    }
    public function ajax($template=null, $attributes=null, $content=null){
        global $kernel, $symbionts;
        if(isset($_POST['add'])){
            $symbiont=Data::symbiont($_POST['add']);
            $kernel->addSymbiont($symbiont);
            $info=Design::symbiontExplode($symbiont);
            print '{"symbiont": "'.($symbionts->$symbiont->_add($info)).'"}';
        }
        elseif(isset($_POST['edit'])){
            $symbiont=Data::symbiont($_POST['edit']);
            $info=Design::symbiontExplode($symbiont);
            $symbiontName=$info->symbiont;
            $kernel->addSymbiont($symbiontName);
            print $symbionts->$symbiontName->_edit($info);
        }
        elseif(isset($_POST['delete'])){
            $symbiont=Data::symbiont($_POST['delete']);
            $info=Design::symbiontExplode($symbiont);
            $symbiontName=$info->symbiont;
            $kernel->addSymbiont($symbiontName);
            print '{"status": "'.($symbionts->$symbiontName->_delete($info)).'"}';
        }
        elseif(isset($_POST['info'])){
            $symbiont=Data::symbiont($_POST['info']);
            $info=Design::symbiontExplode($symbiont);
            $symbiontName=$info->symbiont;
            $kernel->addSymbiont($symbiontName);
            print json_encode($symbionts->$symbiontName->_info($info));
        }
        elseif(isset($_POST['pages'])){
            $l=Data::safe($_POST['pages']);
            $kernel->addSymbiont('Pages');
            print json_encode($symbionts->Pages->_pages($l));
        }
        elseif(isset($_POST['synch'])){
            $l=Data::safe($_POST['synch']);
            $kernel->addSymbiont('Pages');
            print json_encode($symbionts->Pages->_synch($l));
        }
        elseif(isset($_POST['admin'])){
            $symbiont=Data::symbiont($_POST['admin']);
            $info=Design::symbiontExplode($symbiont);
            $symbiontName=$info->symbiont;
            $kernel->addSymbiont($symbiontName);
            print $symbionts->$symbiontName->_admin($info);
        }
    }
}
?>