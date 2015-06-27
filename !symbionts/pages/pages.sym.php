<?
//Pages 0.2.7
class SPages extends Symbiont{
    private $templatePage='';
    private $templateTitle='';
    public function main($template=null, $attributes=null, $content=null){
        
    }
    public function admin($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design, $symbionts,$user;
        if($user->accessLevel<9) return;
        if($kernel->link->param2){
            $kernel->addSymbiont('Pages-Page');
            $symbionts->PagesPage->admin();
        }    
        else{
            $template=$this->_check($template, 'admin');
            
            $design->show($template, array());
        }
    }
    public function pages($template=null, $attributes=null, $content=null){
        global $design, $user;
        if($user->accessLevel<9) return;
        $template=$this->_check($template, 'pages');
        $design->show($template, array());
    }
    public function getPages($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design, $user;
        if($user->accessLevel<9) return;
        $attributes=Data::extend(array(
            'parentId'=>0,
            'level'=>0,
            'selectedId'=>0,
            'neglectId'=>0
        ),$attributes);
        $parentId=$attributes['parentId'];
        
        $pages=$db->query('
            SELECT p.id, p.alias, p.languageId, p.is404, p.redirectId, p.accessLevel, p.title, p.isActive, p.isHome, p.isHidden,
            exists(
                SELECT 1
                FROM `pages` as sub
                WHERE sub.parentId=p.id
            ) as sub
            FROM `pages` as p
            WHERE p.parentId='.$parentId.' AND p.languageId = '.$kernel->lang->id.'
            ORDER BY p.position
        ');
        $return='';
        $level=$attributes['level']++;
        if(is_array($pages)){
            foreach($pages as $page){
                $attributes['parentId']=$page['id'];
                if($page['sub']) $page['subpages']=$this->getPages($template, $attributes, $content);
                else $page['subpages']='';
                if($page['redirectId']) $page['redirect']=$this->getTitle('', array('id'=>$page['redirectId']));
                else $page['redirect']='';
                
                $page['level']=$level;
                $page['selectedId']=$attributes['selectedId'];
                $page['neglectId']=$attributes['neglectId'];
                $return.=$design->run($this->templatePage, $page);
            }
            $return=$design->run($content, array('pages'=>$return, 'level'=>$level));
        }
        return $return;
    }
    public function getTitle($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design;
        $parentId=isset($attributes['id'])?$attributes['id']:0;
        $page=$db->query('
            SELECT p.id, p.alias, p.redirectId, p.title
            FROM `pages` as p
            WHERE p.id='.$parentId.' AND p.languageId = '.$kernel->lang->id.'
            ORDER BY p.position
        ', true);
        $return='';
        if(is_array($page)){
            if($page['redirectId']) $page['redirect']=$this->getTitle('', array('id'=>$page['redirectId']));
            else $page['redirect']='';
            $return=$design->run($this->templateTitle, $page);
        }
        return $return;
    }
    public function setPage($template=null, $attributes=null, $content=null){
        $this->templatePage=$content;
    }
    public function setTitle($template=null, $attributes=null, $content=null){
        $this->templateTitle=$content;
    }
    public function change($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        $template=$this->_check($template, 'change');
        
        if($user->accessLevel<9){
            return $labels->get('errors.prerogatives');
        }
        $id=isset($_POST['id'])?Data::number($_POST['id']):0;
        if($id){
            $vars=$db->select('pages', array('id', 'alias', 'title', 'keywords', 'description', 'accessLevel', 'parentId', 'redirectId', 'template', 'position', 'isHome', 'is404', 'isActive', 'isHidden'), array('id'=>$id, 'languageId'=>$kernel->lang->id), '', 1);
            $vars['languages']=$db->query('
                SELECT
                    l.id,
                    l.abbr,
                    l.isDefault,
                    l.title as languageTitle,
                    p.alias,
                    p.title,
                    p.keywords,
                    p.description
                FROM `languages` as l
                LEFT JOIN `pages` as p
                    ON p.languageId=l.id AND p.id='.$id.'
                WHERE l.isEnabled=1
                ORDER BY l.position
            ');
        }
        else{
            $id=0;
            $vars=array(
                'id'=>'0',
                'accessLevel'=>'0',
                'parentId'=>'',
                'redirectId'=>'',
                'alias'=>'',
                'title'=>'',
                'keywords'=>'',
                'description'=>'',
                'position'=>0,
                'parentId'=>0,
                'redirectId'=>0,
                'isHome'=>0,
                'is404'=>0,
                'isActive'=>1,
                'isHidden'=>0
            );
            $vars['languages']=$db->query('
                SELECT
                    l.id,
                    l.abbr,
                    l.isDefault,
                    l.title as languageTitle,
                    "" AS alias,
                    "" AS title,
                    "" AS keywords,
                    "" AS description
                FROM `languages` as l
                WHERE l.isEnabled=1
                ORDER BY l.position
            ');
        }
        
        $vars['aliasesInTranslit']=$kernel->conf->aliasesInTranslit;
        $vars['aliasesLanguage']=$kernel->conf->aliasesLanguage;
        $vars['abbreviations']=$kernel->conf->abbreviations;
        
        $vars['accessLevels']=$db->query('
            SELECT
                accessLevel,
                title
            FROM `accesslevels`
            WHERE languageId='.$kernel->lang->id.'
            ORDER BY accessLevel
        ');
        $templates='!templates/';
        $files=Data::read($templates);
        $vars['templates']=array();
        foreach($files as $file){
            array_push($vars['templates'], array(
                'simple'=>$templates.$file.'/'.$file.'.png',
                'templateFolder'=>$file
            ));
        }
        $config=json_decode(file_get_contents('db/pages.json'));
        if(!isset($vars['template'])) $vars['template']=$config->template;
        $pos=strrpos($vars['template'], '/');
        if($pos>=0) $vars['templateFolder']=substr($vars['template'], 0, $pos);
        $vars['parent']=$design->take($this->_check('select'), array('selectedId'=>$vars['parentId'], 'neglectId'=>$id));
        $vars['redirect']=$design->take($this->_check('select'), array('selectedId'=>$vars['redirectId'], 'neglectId'=>$id));
        $design->show($template, $vars);
    }
    public function readTemplate($template=null, $attributes=null, $content=null){
        global $labels;
        if(!isset($_POST['template'])) return;
        $template=Data::safe($_POST['template']);
        $path='!templates/'.$template;
        $files=Data::read($path.'/', '/.*\.des\.html/', '/(simple\.des\.html)/');
        $ret=array();
        $labels->import($path.'/labels/');
        foreach($files as $key=>$file){
            $file=substr($file, 0, -9);
            $ret[$key]=array(
                'title'=>$labels->get('templates.'.$file),
                'templateFolder'=>$template,
                'template'=>$template.'/'.$file
            );
        }
        print json_encode($ret);
    }
    public function delete($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design, $user;
        if($user->accessLevel<9) return;
        $template=$this->_check($template, 'delete');
        $design->show($template);
    }
    public function dbChange($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $labels;
        if($user->accessLevel<9){
            return ('{"error":"'.$labels->get('errors.prerogatives').'"}');
        }
        if(!isset($_POST['id'])||!isset($_POST['position'])||!isset($_POST['parentId'])||!isset($_POST['redirectId'])||!isset($_POST['accessLevel'])||!isset($_POST['template'])||!isset($_POST['isHome'])||!isset($_POST['is404'])||!isset($_POST['isActive'])||!isset($_POST['isHidden'])){
            return ('{"error":"'.$labels->get('errors.parametrs').'"}');
        }
        $values=array();
        $where=array();
        $id=Data::number($_POST['id']);
        if($id) $where['id']=$id;
        $values['accessLevel']=Data::number($_POST['accessLevel']);
        $values['parentId']=Data::number($_POST['parentId']);
        $values['redirectId']=Data::number($_POST['redirectId']);
        $values['isHome']=Data::number($_POST['isHome']);
        $values['is404']=Data::number($_POST['is404']);
        $values['isActive']=Data::number($_POST['isActive']);
        $values['isHidden']=Data::number($_POST['isHidden']);
        $values['position']=Data::number($_POST['position']);
        $values['template']=Data::safe($_POST['template']);
        if(isset($_POST['alias'])) $values['alias']=Data::safe($_POST['alias']);
        if(isset($_POST['title'])) $values['title']=Data::safe($_POST['title']);
        if(isset($_POST['keywords'])) $values['keywords']=Data::safe($_POST['keywords']);
        if(isset($_POST['description'])) $values['description']=Data::safe($_POST['description']);
        if(isset($_POST['languages'])){
            foreach($_POST['languages'] as $key=>$val){
                $where['languageId']=Data::number($key);
                $values['title']=Data::safe($val['title']);
                $values['keywords']=Data::safe($val['keywords']);
                $values['description']=Data::safe($val['description']);
                if(isset($val['alias'])) $values['alias']=Data::safe($val['alias']);
                $r=0;
                $r=$db->insert('pages', array_merge($values, $where));
                if(!$r){
                    $r=$db->update('pages', $values, $where);
                }
                else{
                    $where['id']=$r;
                }
            }
        }
        else{
            $where['languageId']=$kernel->lang->id;
            $r=0;
            $r=$db->insert('pages', array_merge($values, $where));
            if(!$r){
                $r=$db->update('pages', $values, $where);
            }
        }
        if($r){
            if(!$id) $db->update('pages', array('position'=>$r), array('id'=>$r));
            return '{"success":""}';
        }
        else{
            return '{"error":"'.$labels->get('symbionts.pages.error').'"}';
        }
    }
    public function dbDelete($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $labels;
        
        if($user->accessLevel<9){
            return ('{"error":"'.$labels->get('errors.prerogatives').'"}');
        }
        if(!isset($_POST['id'])){
            return ('{"error":"'.$labels->get('errors.parametrs').'"}');
        }
        $delete=true;
        if(is_array($_POST['id'])){
            $ids=$_POST['id'];
            foreach($ids as $key=>$val){
                $id=Data::number($val);
                $delete=$delete&&$this->deleteItem($id);
            }
        }
        else{
            $id=Data::number($_POST['id']);
            $delete=$delete&&$this->deleteItem($id);
        }
        if($delete){
            return '{"success":""}';
        }
        else{
            return '{"errror":""}';
        }
    }
    public function deleteItem($id){
        global $db, $user;
        if($user->accessLevel<9) return false;
        $subs=$db->select('pages', 'id', array('parentId'=>$id));
        if(is_array($subs))
        foreach($subs as $sub){
            $this->deleteItem($sub['id']);
        }
        return $db->delete('pages', array('id'=>$id));
    }
    public function dbHome($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $labels, $user;
        if($user->accessLevel<9) return;
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['id'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        $id=data::number($_POST['id']);
        $db->update('pages', array('isHome'=>0));
        $db->update('pages', array('isHome'=>1), array('id'=>$id));
    }
    public function db404($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        if($user->accessLevel<9) return;
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['id'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        $id=data::number($_POST['id']);
        $db->update('pages', array('is404'=>0));
        $db->update('pages', array('is404'=>1), array('id'=>$id));
    }
    public function dbActive($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
       if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['id'])||!isset($_POST['status'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        $id=data::number($_POST['id']);
        $status=data::number($_POST['status']);
        $db->update('pages', array('isActive'=>$status), array('id'=>$id));
        print('{"success":'.($status==1?'"Actived"':'"Disactivate"').'}');
    }
    public function dbHidden($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
       if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['id'])||!isset($_POST['status'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        $id=data::number($_POST['id']);
        $status=data::number($_POST['status']);
        $db->update('pages', array('isHidden'=>$status), array('id'=>$id));
        print('{"success":'.($status==1?'"Hidden"':'"Show"').'}');
    }
    public function dbSort($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
       if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['sort'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        foreach($_POST['sort'] as $key=>$val){
            $id=data::number($val);
            $position=data::number($key)+1;
            $db->update('pages', array('position'=>$position), array('id'=>$id));
        }
    }
    public function setAsMain($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        
        $labels->import('db/labels/errors/');
        if($user->accessLevel<9){
            print('{"error":"'.$labels->get('errors.prerogatives').'"}');
            return;
        }
        if(!isset($_POST['id'])||!isset($_POST['main'])){
            print('{"error":"'.$labels->get('errors.parametrs').'"}');
            return;
        }
        $id=Data::number($_POST['id']);
        $main=Data::symbiont($_POST['main']);
        $db->update('pages', array('symbiont'=>$main), array('id'=>$id));
    }
}
?>