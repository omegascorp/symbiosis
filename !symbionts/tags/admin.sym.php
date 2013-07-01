<?
class STags_Admin extends Symbiont{
    public function __construct(){
        parent::__construct('Tags');
    }
    public function main($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db;
        $attributes=Data::extend(array(
            'isGlobal'=>true
        ),$attributes);
        
        $template=$this->_check($template, 'admin');
        $vars=array('isGlobal'=>$attributes['isGlobal']);
        $vars['tags']=$db->select('stags', array('id', 'alias', 'title', 'popularity'), array('languageId'=>$kernel->lang->id), 'popularity DESC');
        
        $design->show($template, $vars);
    }
    public function get($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db;
        
        $limit=20;
        $title='';
        if(isset($_POST['limit'])) $limit=Data::number($_POST['limit']);
        if(isset($_POST['title'])) $title=Data::safe($_POST['title']);
        
        $where='languageId='.$kernel->lang->id;
        if($title) $where.=' AND title LIKE "'.$title.'%"';
        
        $tags=$db->select('stags', array('id', 'title', 'alias'), $where, 'popularity DESC', $limit);
        return json_encode($tags);
    }
    public function button($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db, $labels;
        $attributes=Data::extend(array(
            'symbiont'=>'',
            'itemId'=>0
        ),$attributes);
        
        $vars=array();
        $vars['symbiont']=Data::symbiont($attributes['symbiont']);
        $vars['itemId']=Data::number($attributes['itemId']);
        
        if($vars['itemId']){
            $vars['tags']=$db->query('
                SELECT t.id, t.title, t.alias
                FROM `stagsconnections` as c
                    LEFT JOIN `stags` as t
                ON t.id=c.tagId AND t.languageId='.$kernel->lang->id.'
                WHERE c.itemId='.$vars['itemId'].' AND c.symbiont="'.$vars['symbiont'].'"
                ORDER BY t.popularity DESC
            ');
        }
        else{
            $vars['tags']='';
        }
        $template=$this->_check($template, 'admin-button');
        $design->show($template, $vars);
    }
    public function select($template=null, $attributes=null, $content=null){
        global $kernel, $design, $db;
        
        $vars=array();
        if(isset($_POST['tags'])){
            $tags=$_POST['tags'];
            foreach($tags as $key=>$val){
                $tags[$key]=Data::number($val);
            }
        }
        if(isset($tags)){
            $vars['tags']=$db->select('stags', array('id', 'alias', 'title'), array('id'=>$tags, 'languageId'=>$kernel->lang->id), 'popularity');
        }
        else{
            $vars['tags']=array();
        }
        $vars['languages']=$db->query('
            SELECT
                l.id,
                l.abbr,
                l.isDefault
            FROM `languages` as l
            WHERE l.isEnabled=1
            ORDER BY l.position
        ');
        
        $template=$this->_check($template, 'admin-select');
        $design->show($template, $vars);
    }
    public function change($template=null, $attributes=null, $content=null){
        $template=$this->_check($template, 'change');
        global $design, $db, $kernel;
        $id=isset($_POST['id'])?Data::number($_POST['id']):0;
        if($id){
            $vars=$db->select('stags', array('id', 'alias', 'title'), array('id'=>$id, 'languageId'=>$kernel->lang->id), '', 1);
            $vars['languages']=$db->query('
                SELECT
                    l.id,
                    l.abbr,
                    l.isDefault,
                    m.alias,
                    m.title
                FROM `languages` as l
                    LEFT JOIN `stags` as m ON m.id='.$id.' AND m.languageId=l.id
                WHERE l.isEnabled=1
                ORDER BY l.position
            ');
        }
        else{
            $vars=array(
                'id'=>$id,
                'title'=>'',
                'alias'=>''
            );
            $vars['languages']=$db->query('
                SELECT
                    l.id,
                    l.abbr,
                    l.isDefault,
                    "" AS alias,
                    "" AS title
                FROM `languages` as l
                WHERE l.isEnabled=1
                ORDER BY l.position
            ');
        }
        $vars['aliasesInTranslit']=$kernel->conf->aliasesInTranslit;
        $vars['aliasesLanguage']=$kernel->conf->aliasesLanguage;
        $vars['abbreviations']=$kernel->conf->abbreviations;
        if($kernel->conf->aliasesInTranslit){
            if(count($vars['languages'])>1){
                foreach($vars['languages'] as $key=>$language){
                    $translit='symbionts/languages/translit/'.$language['abbr'].'.json';
                    if(file_exists($translit)){
                        $vars['languages'][$key]['translit']=file_get_contents($translit);
                    }
                    else{
                        $vars['languages'][$key]['translit']="{}";
                    }
                }
            }
            else{
                $translit='symbionts/languages/translit/'.$kernel->lang->abbr.'.json';
                $vars['translit']=file_get_contents($translit);
            }
        }
        $design->show($template, $vars);
    }
    public function delete($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        if($user->accessLevel<9){
            print($labels->get('errors.prerogatives'));
            return;
        }
        if(!isset($_POST['id'])){
            print($labels->get('errors.parametrs'));
            return;
        }
        if(is_array($_POST['id'])){
            $id=$_POST['id'];
            foreach($id as $key=>$val){
                $id[$key]=Data::number($val);
            }
        }
        else{
            $id=Data::number($_POST['id']);
        }
        $vars=array(
            'id'=>$id
        );
        $design->show('symbionts/tags/delete', $vars);
    }
    public function dbChange($template=null, $attributes=null, $content=null){
        global $kernel, $db;
        $alias=isset($_POST['alias'])?Data::safe($_POST['alias']):'';
        $id=isset($_POST['id'])?Data::number($_POST['id']):'';
        if(isset($_POST['languages'])){
            foreach($_POST['languages'] as $key=>$language){
                $where=array();
                $where['languageId']=Data::number($key);
                $where['id']=$id;
                
                $values['title']=Data::safe($language['title']);
                $values['alias']=isset($language['alias'])?Data::safe($language['alias']):$alias;
                $values['languageId']=$where['languageId'];
                if($id) $values['id']=$id;
                
                if($values['alias']==='') continue;
                $r=$db->insert('stags', $values);
                if(!$r){
                    $db->update('stags', $values, $where);
                }
                else{
                    $values['id']=$r;
                }
            }
            if(!$id){
                $db->update('stags', array('position'=>$values['id']), array('id'=>$values['id']));
            }
            return '{"success":""}';
        }
        elseif(isset($_POST['title'])&&isset($_POST['alias'])){
            
            return '{"success":""}';
        }
    }
    public function dbDelete($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['id'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        if(is_array($_POST['id'])){
            $ids=$_POST['id'];
            foreach($ids as $key=>$val){
                $id=Data::number($val);
                $db->delete('stags', array('id'=>$id));
            }
        }
        else{
            $id=Data::number($_POST['id']);
            $db->delete('stags', array('id'=>$id));
        }
        print('{"success":""}');
    }
}
?>