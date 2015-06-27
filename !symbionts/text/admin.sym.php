<?
class SText_Admin extends Symbiont{
    public function main($template=null, $attributes=null, $content=null){
        global $kernel, $design;
        $attributes=Data::extend(array(
            'id'=>''
        ), $attributes);
        $id=Data::fileName($attributes['id']);
        if(!$id){
            return;
        }
        $file='db/symbiont-text/'.$id.'-'.$kernel->language->abbr.'.html';
        $vars=array();
        $vars['content']=stripslashes(file_get_contents($file));
        
        $template=$this->_check($template, 'main');
        $design->show($template, $vars);
    }
    public function _admin($info=null){
        global $kernel, $design, $db;
        $alias=$kernel->link->param2;
        $id=$db->select('pages', 'id', array('alias'=>$alias, 'languageId'=>$kernel->lang->id), '', 1);
        $vars=array('id'=>$id);
        Data::createFolder('db/symbiont-text/');
        $vars['languages']=$db->query('
            SELECT
                l.id,
                l.abbr,
                l.isDefault,
                l.title as languageTitle
            FROM `languages` as l
            WHERE l.isEnabled=1
            ORDER BY l.position
        ');
        foreach($vars['languages'] as $key=>$language){
            $file='db/symbiont-text/'.$id.'-'.$language['abbr'].'.html';
            if(file_exists($file)){
                $vars['languages'][$key]['content']=stripslashes(file_get_contents($file));
            }
            else{
                $vars['languages'][$key]['content']='';
            }
        }
        $design->show('symbionts/text/_admin', $vars);
    }
    public function dbSave($template=null, $attributes=null, $content=null){
        global $labels, $kernel, $user;
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            return '{"error":"'.$labels->get('errors.prerogatives').'"}';
        }
        if(!isset($_POST['id'])||!isset($_POST['languages'])){
            return '{"error":"'.$labels->get('errors.parametrs').'"}';
        }
        Data::createFolder('db/symbiont-text/');
        $draft=isset($_POST['draft'])&&$_POST['draft']?'-d':'';
        $id=Data::fileName($_POST['id']);
        foreach($_POST['languages'] as $abbr=>$val){
            $abbr=Data::fileName($abbr);
            $content=Data::safe($val['content']);
            $file='db/symbiont-text/'.$id.'-'.$abbr.$draft.'.html';
            $f=fopen($file, 'w');
            fwrite($f, $content);
            fclose($f);
        }
        return '{"success":""}';
    }
    public function dbRead($template=null, $attributes=null, $content=null){
        global $labels, $kernel, $user, $db;
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            return '{"error":"'.$labels->get('errors.prerogatives').'"}';
        }
        if(!isset($_POST['id'])){
            return '{"error":"'.$labels->get('errors.parametrs').'"}';
        }
        $draft=isset($_POST['draft'])&&$_POST['draft']?'-d':'';
        $id=Data::fileName($_POST['id']);
        $languages=$db->query('
            SELECT
                l.abbr
            FROM `languages` as l
            WHERE l.isEnabled=1
            ORDER BY l.position
        ');
        $r=array();
        $r['languages']=array();
        foreach($languages as $key=>$language){
            $file='db/symbiont-text/'.$id.'-'.$language['abbr'].$draft.'.html';
            $r['languages'][$language['abbr']]=array();
            if(file_exists($file)){
                $r['languages'][$language['abbr']]['content']=stripslashes(file_get_contents($file));
            }
            else{
                $r['languages'][$language['abbr']]['content']='';
            }
        }
        return json_encode($r);
    }
}
?>