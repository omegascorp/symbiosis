<?
//Controller 0.0.11
class Controller{
    public function init($path=null){
        global $db, $kernel, $user;
        //Processing current url
        $normalize=false;
        if($path==null){
            $path=$_SERVER['REQUEST_URI'];
            $normalize=true;
        }
        $this->url($path, $normalize);
        //Default language
        $default=$db->select('languages', array('abbr', 'id'), array('isDefault'=>1), '', 1);
        $kernel->lang->defaultId=$default['id'];
        $kernel->lang->defaultAbbr=$default['abbr'];
        //Collecting page data
        $page=$this->getPage(0, $kernel->link->page);
        $kernel->page->add($page);
    }
    //Collecting language data
    public function setLanguage($abbr=''){
        global $db, $kernel;
        if($abbr=='') $abbr=$kernel->link->language;
        $tmp=$db->select("languages", "*", array('abbr'=>$abbr), "", 1);
        $kernel->lang->add($tmp);
        $kernel->conf->base=$kernel->conf->url.($kernel->conf->abbreviations?$kernel->lang->abbr.'/':'');
    }
    //Return the home page alias
    private function getHomePage(){
        global $db, $kernel;
        $page=$db->select("pages", "*", array("isHome"=>1, "languageId"=>$kernel->lang->id), "", 1);
    if(is_array($page)){
        return $page;
    }
    else{
        return $this->get404Page();
    }
    }
    private function get404Page(){
    global $db, $kernel;
    header("HTTP/1.0 404 Not Found");
    $p404=$db->select("pages", "*", array('is404'=>1, "languageId"=>$kernel->lang->id), "", 1);
    if(is_array($p404)){
        return $p404;
    }
    else{
        $kernel->lang->add($this->getDefaultLanguage());
        return $db->select("pages", "*", array('is404'=>1, "languageId"=>$kernel->lang->id), "", 1);
    }
    }
    private function getPage($id=0, $alias=''){
        global $db, $kernel, $user;
        //Get homepage
        if($id==0&&$alias==''){
            return $this->getHomePage();
        }
        else{
            $page=$db->select('pages', '*', array('alias'=>$alias, 'languageId'=>$kernel->lang->id), "", 1);
            if(!is_array($page)||$user->accessLevel<$page['accessLevel']||!$page['isActive']){
                $page=$this->get404Page();
            }
            elseif($page['redirectId']){
                return $this->getPage($page['redirectId']);
            }
        }
        return $page;
    }
    //Return the default language abbreviation
    private function getDefaultLanguage($alias=''){
        global $db, $kernel;
        if($alias&&!$kernel->conf->abbreviations){
        $default=' AND l.isDefault=1';
        $query='SELECT l.id, l.abbr, l.code, l.title, l.titleEn, l.isEnabled, l.isDefault, l.position
            FROM pages AS p
            LEFT JOIN languages as l
            ON l.id=p.languageId
            WHERE p.alias="'.$alias.'" AND l.isEnabled=1 '.$default.'
            LIMIT 1';
        $res=$db->query($query, true);
        if(is_array($res)){
        return $res;
        }
    }
        return $db->select("languages", "*", array("isDefault"=>1), "", 1);
    }
    //Apply short links
    private function shortlinks($path){
    $elements=explode("/", $path);
    $shortlinks=json_decode(file_get_contents('db/shortlinks.json'), true);
    $i=0;
    $newPath='';
    while(true){
        if(!isset($elements[$i])) break;
        $key=$elements[$i];
        $i++;
        if(!isset($shortlinks[$key])){
        if($i==1) return $path;
        $newPath.='/'.$key;
        }
        else{
        $newPath=$shortlinks[$key];
        }
    }

    return $newPath;
    }
    //Collect current url
    private function url($path, $normalize=true){
        global $db, $kernel;
        if($path!=$kernel->conf->path){
            //Decode url
            $page=rawurldecode($path);
        $pos=strpos($page,"?");
        if($pos) $page=substr($page, 0, $pos);
        if($normalize){
        //Delete path and postfix
        $pathLen=strlen($kernel->conf->path);
        $page=substr($page,$pathLen);
        $postfixLen=strlen($kernel->conf->postfix);
        if($postfixLen&&substr($page, -strlen($kernel->conf->postfix))==$kernel->conf->postfix){
            $page=substr($page, 0, -$postfixLen);
        }
        }

        //Set full link
            $kernel->link->full=$page;
        $kernel->link->current=$kernel->conf->url.$page.$kernel->conf->postfix;

        //Apply short links if it need
        if($kernel->conf->shortlinks){
        $page=$this->shortlinks($page);
        }
            
        //Seporate the url by the slashes
        $elements=explode("/", $page);

        $hasAbbr=false;
        if(is_array($elements)&&isset($elements[0])){
        if($kernel->conf->abbreviations){
            if($db->count('languages', array('abbr'=>$elements[0]), 1)){
            $kernel->link->language=$elements[0];
            unset($elements[0]);
            if(isset($elements[1])){
                $kernel->link->page=$elements[1];
                unset($elements[1]);
            }
            $pos=strpos($page, "/");
            $kernel->link->relative=$pos?substr($page, $pos+1):'';
            $pos=strpos($kernel->link->relative, "/");
            $kernel->link->params=$pos?substr($kernel->link->relative, $pos+1):'';
            $hasAbbr=true;
            }
        }
        if(!$hasAbbr){
            $kernel->link->page=$elements[0];
            $kernel->link->relative=$page;
            $pos=strpos($kernel->link->relative, "/");
            $kernel->link->params=$pos?substr($kernel->link->relative, $pos+1):'';
            unset($elements[0]);
        }
        $i=1;
        foreach($elements as $element){
            $kernel->link->set('param'.($i++), $element);
        }
        }
    }
    //Select brouser language
    if(!$kernel->link->language&&$kernel->conf->brouserLanguage){
        $langs=$this->getBrouserLanguages();
        while($abbr=$langs->read()){
        if($db->count('languages', array('abbr'=>$abbr, 'isEnabled'=>1), 1)){
            $kernel->link->language=$abbr;
            break;
        }
        }
    }
    //Select default language
    if(!$kernel->link->language){
        $kernel->lang->add($this->getDefaultLanguage($kernel->link->page));
        $kernel->link->language=$kernel->lang->abbr;
    }
    $this->setLanguage($kernel->link->language);
    }
    public function getBrouserLanguages(){
    $languages=new Set();
    if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])){
        $langs=explode(',', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
        foreach($langs as $lang){
        $pos=strpos($lang, ';');
        if($pos!==false){
            $lang=substr($lang, 0, $pos);
        }
        $pos=strpos($lang, '-');
        if($pos!==false){
            $lang=substr($lang, 0, $pos);
        }
        $languages->push($lang);
        }
    }
    return $languages;
    }
}
?>
