<?
//Page 0.0.1
class SPages_Page extends Symbiont{
    public function __construct(){
        parent::__construct('Pages');
    }
    public function main($template=null, $attributes=null, $content=null){
        
    }
    public function admin($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design, $labels, $symbionts, $user;
        if($user->accessLevel<9) return;
        $alias=$kernel->link->param2;
        $page=$db->select('pages', array('id', 'template', 'title', 'symbiont'), array('alias'=>$alias, 'languageId'=>$kernel->lang->id), '', 1);
        
        $vars=$page;
        
        $content='';
        if($page['symbiont']){
            $info=new SymbiontInfo($page['symbiont']);
            $name=$info->symbiont;
            if($kernel->isExistsSymbiont($name)){
                $kernel->addSymbiont($name);
                ob_start();
                $symbionts->$name->_admin($info);
                $content=ob_get_clean();
            }
        }
        
        //Data::createFolder('temp/plugins/');
        if(file_exists('temp/plugins.json')){
            $plugins=json_decode(file_get_contents('temp/plugins.json'), true);
        }
        else{
            $dir=opendir("!symbionts");
            $plugins=array();
            while($file=readdir($dir)){
                if(($file!=".")&&($file!="..")&&($file[0]!='.')){
                    $config='!symbionts/'.$file.'/config.json';
                    $label='!symbionts/'.$file.'/labels/';
                    $labels->import($label);
                    if(file_exists($config)){
                        $conf=json_decode(file_get_contents($config), true);
                        if(!isset($conf['plugin'])||!$conf['plugin']){ continue; }
                        $plugins[$conf['symbiont']]=$labels->get('symbionts.'.$file.'.plugin');
                    }
                }
            }
            $f=fopen('temp/plugins.json', 'w');
            fwrite($f, json_encode($plugins));
            fclose($f);
        }
        
        
        $vars['content']=$content;
        $vars['plugins']=$plugins;
        $vars['symbiont']=$page['symbiont'];
        $pos=strpos($page['symbiont'], '.');
        $vars['symbiontName']=$pos==null?$page['symbiont']:substr($page['symbiont'], 0, $pos);
        $vars['title']=$page['title'];
        $design->show('symbionts/pages/page', $vars);
    }
    public function dbSet($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $labels;
        
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            return '{"error":"'.$labels->get('errors.prerogatives').'"}';
        }
        if(!isset($_POST['id'])||!isset($_POST['plugin'])){
            return '{"error":"'.$labels->get('errors.parametrs').'"}';
        }
        
        $id=(int)($_POST['id']);
        $plugin=Data::safe($_POST['plugin']);
        
        $up=$db->update('pages', array('symbiont'=>$plugin), array('id'=>$id));
        if($up){
            return '{"success":""}';
        }
        return '{"error":""}';
    }
    public function dbRead($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels, $symbionts;
        
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['alias'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        
        $alias=Data::word($_POST['alias']);
        $positions=new Positions(null, $alias);
        $positions->read();
        $widgets=$positions->get();
        $result=array();
        foreach($widgets as $widget){
            $exp=Design::symbiontExplode($widget->symbiont);
            $symbiont=$exp->symbiont;
            $kernel->addSymbiont($symbiont);
            $info=$symbionts->$symbiont->_info($exp);
            array_push($result, array(
                'symbiont'=>$widget->symbiont,
                'accessLevel'=>$widget->accessLevel,
                'title'=>$info['title'],
                'block'=>$info['block'],
                'icon'=>'!symbionts/'.strtolower($symbiont).'/icons/32.png'
            ));
        }
        return json_encode($result);
    }
    public function dbSave($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['positions'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        if(is_array($_POST['positions'])){
            foreach($_POST['positions'] as $alias=>$widgets){
                $positions=new Positions(null, $alias);
                if(is_array($widgets)){
                    foreach($widgets as $key=>$widget){
                        $position=new Position(null, $alias, $key+1, $widget['symbiont'], $widget['accessLevel']);
                        $positions->add($position);
                    }
                }
                $positions->save();
            }
        }
    }
}
?>