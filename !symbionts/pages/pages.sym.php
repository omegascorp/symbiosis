<?
//Pages 0.2.7
class SPages extends Symbiont{
    private $templatePage='';
    private $templateTitle='';
    public function main($template=null, $attributes=null, $content=null){
        
    }
    public function admin($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design, $symbionts;
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
        global $design;
        $template=$this->_check($template, 'pages');
        $design->show($template, array());
    }
    public function getPages($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design;
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
    public function addText($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        $template=$this->_check($template, 'addText');
        
        $vars=array(
            'alias'=>'',
            'title'=>''
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
        
        $config=json_decode(file_get_contents('db/pages.json'));
        $vars['template']=$config->template;
        
        $vars['aliasesInTranslit']=$kernel->conf->aliasesInTranslit;
        $vars['aliasesLanguage']=$kernel->conf->aliasesLanguage;
        $vars['abbreviations']=$kernel->conf->abbreviations;
        if($kernel->conf->aliasesInTranslit){
            if(count($vars['languages'])>1){
                foreach($vars['languages'] as $key=>$language){
                    $translit='db/translit/'.$language['abbr'].'.json';
                    if(file_exists($translit)){
                        $vars['languages'][$key]['translit']=file_get_contents($translit);
                    }
                    else{
                        $vars['languages'][$key]['translit']="{}";
                    }
                }
            }
            else{
                $translit='db/translit/'.$kernel->lang->abbr.'.json';
                $vars['translit']=file_get_contents($translit);
            }
        }
        $design->show($template, $vars);
    }
    public function addBlog($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        $template=$this->_check($template, 'addBlog');
        
        $vars=array(
            'alias'=>'',
            'title'=>''
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
        
        $config=json_decode(file_get_contents('db/pages.json'));
        $vars['template']=$config->template;
        
        $vars['aliasesInTranslit']=$kernel->conf->aliasesInTranslit;
        $vars['aliasesLanguage']=$kernel->conf->aliasesLanguage;
        $vars['abbreviations']=$kernel->conf->abbreviations;
        if($kernel->conf->aliasesInTranslit){
            if(count($vars['languages'])>1){
                foreach($vars['languages'] as $key=>$language){
                    $translit='db/translit/'.$language['abbr'].'.json';
                    if(file_exists($translit)){
                        $vars['languages'][$key]['translit']=file_get_contents($translit);
                    }
                    else{
                        $vars['languages'][$key]['translit']="{}";
                    }
                }
            }
            else{
                $translit='db/translit/'.$kernel->lang->abbr.'.json';
                $vars['translit']=file_get_contents($translit);
            }
        }
        $design->show($template, $vars);
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
            FROM `accessLevels`
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
        global $db, $kernel, $design;
        $template=$this->_check($template, 'delete');
        $design->show($template);
    }
    public function dbAddText($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $labels;
        
        if($user->accessLevel<9){
            return ('{"error":"'.$labels->get('errors.prerogatives').'"}');
        }
        if(!isset($_POST['languages'])||!isset($_POST['template'])){
            return ('{"error":"'.$labels->get('errors.parametrs').'"}');
        }
        $values=array();
        $where=array();
        
        $values['template']=Data::safe($_POST['template']);
        $alias='';
        if(isset($_POST['alias'])) $alias=$values['alias']=Data::safe($_POST['alias']);
        if(isset($_POST['title'])) $values['title']=Data::safe($_POST['title']);
        if(isset($_POST['languages'])){
            foreach($_POST['languages'] as $key=>$val){
                $where['languageId']=Data::number($key);
                $values['title']=Data::safe($val['title']);
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
            $position=new Position(null, 'Page.'.$r, 1);
            
            $db->update('pages', array('position'=>$r), array('id'=>$r));
            
            $values=array('categoryId'=>1);
            foreach($_POST['languages'] as $key=>$language){
                $where=array();
                $where['languageId']=Data::number($key);
                
                $values['title']=Data::safe($language['title']);
                $values['alias']=isset($language['alias'])?Data::safe($language['alias']):$alias;
                $values['languageId']=$where['languageId'];
                $values['text']=Data::safe($language['content']);
                
                if($values['alias']==='') continue;
                $r=$db->insert('snotes', $values);
                if(!$r){
                    $db->update('snotes', $values, $where);
                }
                else{
                    $values['id']=$r;
                }
            }
            
            if($r){
                $position->symbiont='Notes.main.categoryId=1.noteId='.$r;
                $position->save();
                $db->update('snotes', array('position'=>$values['id']), array('id'=>$values['id']));
            }
            
            return '{"success":""}';
        }
        else{
            return '{"errror":""}';
        }
    }
    public function dbAddBlog($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $labels;
        
        if($user->accessLevel<9){
            return ('{"error":"'.$labels->get('errors.prerogatives').'"}');
        }
        if(!isset($_POST['languages'])||!isset($_POST['template'])){
            return ('{"error":"'.$labels->get('errors.parametrs').'"}');
        }
        $values=array();
        $where=array();
        
        
        $values['template']=Data::safe($_POST['template']);
        if(isset($_POST['alias'])) $values['alias']=Data::safe($_POST['alias']);
        if(isset($_POST['title'])) $values['title']=Data::safe($_POST['title']);
        
        if(isset($_POST['languages'])){
            foreach($_POST['languages'] as $key=>$val){
                $where['languageId']=Data::number($key);
                $values['title']=Data::safe($val['title']);
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
            $position=new Position(null, 'Page.'.$r, 1);
            
            $db->update('pages', array('position'=>$r), array('id'=>$r));
            
            $values=array('for'=>'notes', 'settings'=>'{"order":"0"}');
            if(isset($_POST['alias'])) $values['alias']=Data::safe($_POST['alias']);
            if(isset($_POST['title'])) $values['title']=Data::safe($_POST['title']);
            foreach($_POST['languages'] as $key=>$language){
                $where=array();
                $where['languageId']=Data::number($key);
                
                $values['title']=Data::safe($language['title']);
                if(isset($language['alias'])) $values['alias']=Data::safe($language['alias']);
                $values['languageId']=$where['languageId'];
                
                if($values['alias']==='') continue;
                $r=$db->insert('scategories', $values);
                if(!$r){
                    $db->update('scategories', $values, $where);
                }
                else{
                    $values['id']=$r;
                }
            }
            
            if($r){
                $position->symbiont='Notes.main.categoryId='.$r;
                $position->save();
                $db->update('scategories', array('position'=>$values['id']), array('id'=>$values['id']));
            }
            
            return '{"success":""}';
        }
        else{
            return '{"errror":""}';
        }
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
        global $db;
        $subs=$db->select('pages', 'id', array('parentId'=>$id));
        if(is_array($subs))
        foreach($subs as $sub){
            $this->deleteItem($sub['id']);
        }
        return $db->delete('pages', array('id'=>$id));
    }
    public function dbHome($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $labels;
        
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
    public function _pages($link=null){
        global $db, $kernel, $symbionts;
        $id=0;
        $add=array();
        $pages=array();
        if($link){
            $ids=explode('/', $link);
            if(isset($ids[0])){
                $id=$ids[0];
                $symbiont=$db->select('pages', 'symbiont', array('id'=>$id, 'languageId'=>$kernel->lang->id), '', 1);
                if($symbiont){
                    $info=Design::symbiontExplode($symbiont);
                    $kernel->addSymbiont($info['symbiont']);
                    $add=$symbionts->$info['symbiont']->_pages($link);
                }
            }
        }
        
        if(!isset($ids[1])){
            $pages=$db->query('
                SELECT title, (
                    SELECT 1
                    FROM pages as p
                    WHERE parentId=pages.id
                    LIMIT 1
                ) OR symbiont!="" as sub,
                symbiont,
                id as synch
                FROM pages
                WHERE parentId='.$id.' AND languageId='.$kernel->lang->id.'
                ORDER BY position
            ');
        }
        
        if(is_array($add)&&is_array($pages)) $pages=array_merge($pages, $add);
        else if(is_array($add)) $pages=$add;
        return $pages;
    }
    public function _synch($link, $id, $menuId){
        global $db, $kernel, $symbionts;
        if(!$link) return array();
        $array=array();
        $ids=explode('/', $link);
        $pageId=$ids[0];
        $info=$db->query('
            SELECT (
                SELECT 1
                FROM pages as s
                WHERE s.parentId=p.id
                LIMIT 1
            ) AS sub,
            p.symbiont
            FROM pages as p
            WHERE p.id='.$pageId.'
            LIMIT 1
        ', 1);
        $page=$db->query('
            SELECT p.title,
            p.id as itemId,
            "'.$id.'" AS id,
            CONCAT(l.abbr, "/", p.alias, "'.$kernel->conf->postfix.'") as link,
            l.id as languageId
            FROM pages as p
            LEFT JOIN languages AS l ON l.id=p.languageId
            WHERE p.id='.$pageId.'
        ');
        //$kernel->addSymbiont('Menu-AdminItems');
        $symbionts->MenuAdminItems->synchronize($page);
        if($info['sub']){
            $this->synch($pageId, $id, $menuId);
        }
            /*
            if($page) $array=array_merge($array, $page);
            if($info['sub']){
                $pages=$db->query('
                    SELECT p.title, (
                        SELECT 1
                        FROM pages as s
                        WHERE s.parentId=p.id
                        LIMIT 1
                    ) AS sub,
                    CONCAT(p.alias, "/", l.abbr, "'.$kernel->conf->postfix.'") as link
                    FROM pages as p
                    LEFT JOIN languages AS l ON l.id=p.languageId
                    WHERE p.parentId='.$id.'
                ');
                if($pages) $array=array_merge($array, $pages);
            };
            */
        
        return $array;
    }
    public function synch($pageParentId, $parentId, $menuId){
        global $symbionts, $db, $kernel;
        $infos=$db->query('
            SELECT (
                SELECT 1
                FROM pages as s
                WHERE s.parentId=p.id
                LIMIT 1
            ) AS sub,
            p.id
            FROM pages as p
            WHERE p.parentId='.$pageParentId.'
            GROUP BY p.id
            ORDER BY p.id
        ');
        
        $page=$db->query('
            SELECT p.title,
            p.id as itemId,
            "'.$menuId.'" as menuId,
            "'.$parentId.'" as parentId,
            1 AS synched,
            CONCAT(l.abbr, "/", p.alias, "'.$kernel->conf->postfix.'") as link,
            l.id as languageId
            FROM pages as p
            LEFT JOIN languages AS l ON l.id=p.languageId
            WHERE p.parentId='.$pageParentId.'
            ORDER BY p.id, p.languageId
        ');
        
        $ids=$symbionts->MenuAdminItems->synchronize($page, $parentId);
        foreach($infos as $key=>$info){
            if($info['sub']){
                $this->synch($info['id'], $ids[$key], $menuId);
            }
        }
        
        //return $ids;
    }
}
?>