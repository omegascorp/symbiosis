<?
//Kernel 0.5.3
class Kernel{
    private $conf;          //Config data
    private $page;          //Page data
    private $link;          //Link data
    private $lang;          //Language data
    private $vars;          //Vars that will be used in temlate
    private $libraries;     //Set of libraries
    private $symbionts;     //Set of symbionts
    public function __construct(){
        include('libraries/kernel/set.lib.php');
        include('libraries/kernel/dictionary.lib.php');
	include('libraries/kernel/stack.lib.php');
        $this->conf=new Dictionary();
        $this->page=new Dictionary();
        $this->link=new Dictionary();
        $this->lang=new Dictionary();
        $this->vars=new Dictionary();
        $this->libraries=new Set();
        $this->symbionts=new Set();
    }
    //Init kernel
    public function init($control=true, $link='', $language=''){
        //Include libraries
        $this->libraries->add(array('Kernel', 'Kernel-Set', 'Kernel-Dictionary', 'Kernel-Stack'));
        $this->libraries->setPointer(4);
        $this->libraries->add(array('User', 'MySQL', 'Design', 'Labels', 'Data', 'Symbiont', 'Symbiont-SymbiontInfo', 'Symbionts', 'Positions', 'Positions-Position'));
        $this->includeLibraries();
	
        //Connect to db
        global $db;
        include('config.php');
        $db=new MySQL($host, $user, $pass, $database);
	
        //Create symbionts
        global $symbionts;
        $symbionts=new Symbionts();
	
        //Collecting config data
        $config=json_decode(file_get_contents('db/config.json'));
        $this->conf->add($config);
	$this->conf->url='http://'.$_SERVER['HTTP_HOST'].$this->conf->path;
        $this->conf->home=$db->select("pages", "alias", array("isHome"=>1, "languageId"=>$this->lang->id), "", 1);
	$this->conf->symbionts=$this->conf->url.'!symbionts/';
	
	//User
        global $user;
	$user=new User();
	$user->autorisation();
	
	if($control||$link){
	    $this->addLibrary('Kernel-Controller');
	    $controler=new Controller();
	    $controler->init($link);
	}
	
	if($language){
	    $language=Data::word($language);
	    $this->setLanguage($language);
	}
	
	$templateFolder=substr($this->page->template, 0, strpos($this->page->template, '/'));
	$this->conf->files=$this->conf->url.'!templates/'.$templateFolder.'/files/';
	$this->conf->css=$this->conf->files.'css/';
	$this->conf->js=$this->conf->files.'js/';
	$this->conf->img=$this->conf->files.'img/';
	
	global $labels;
	$labels=new Labels();
	$labels->import('db/labels/main/');
	
	global $design;
	$design=new Design();
	$design->init();
	
	if($control){
	    $processes=json_decode(file_get_contents('db/processes.json'));
	    foreach($processes as $process){
		Design::symbiontEval($process);
	    }
	}
	
	if($control){
	    $this->addLibrary('Place');
	    Place::init();
	}
    }
    //Destroy Kernel
    public function destroy(){
	global $design;
	$design->destroy();
    }
    
    //Include libraries
    public function includeLibraries(){
	while($l=$this->libraries->read()){
	    $n=explode('-', $l);
	    if(count($n)>1){
		$folder=$n[0];
		$lib=$n[1];
	    }
	    else{
		$folder=$lib=$l;
	    }
	    $fileName='libraries/'.strtolower($folder).'/'.strtolower($lib).'.lib.php';
	    if(file_exists($fileName)){
		include($fileName);
	    }
	    else{
		//"Library «".$l."» not found."
	    }
	}
    }
    //Include symbionts
    public function includeSymbionts(){
	while($s=$this->symbionts->read()){
	    if(substr_count($s, '-')){
		list($symbiontName, $symbiontClass)=explode('-', $s);
	    }
	    else{
		$symbiontClass=$symbiontName=$s;
	    }
	    $fileName='!symbionts/'.strtolower($symbiontName).'/'.strtolower($symbiontClass).'.sym.php';
	    if(file_exists($fileName)){
		include($fileName);
		global $symbionts;
		$sc=$symbiontName!=$symbiontClass?$symbiontName.$symbiontClass:$symbiontName;		
		$symbionts->add($sc, $symbiontName, $symbiontClass);
	    }
	    else{
	       //"Symbiont «".$s."» not found."
	    }
	}
    }
    //Is symbiont exists
    public function isExistsSymbiont($name){
	$s=explode('-', $name);
        if(isset($s[0])&&isset($s[1])){
            $name=$s[0];
            $class=$s[1];
        }
        else{
            $class=$name;
        }
        if(file_exists('!symbionts/'.strtolower($name).'/'.strtolower($class).'.sym.php')) return true;
        return false;
    }
    //Is librariy exists
    public function isExistsLibrary($name){
	$s=explode('-', $name);
        if(isset($s[0])&&isset($s[1])){
            $folder=$s[0];
            $name=$s[1];
        }
        else{
            $folder=$name;
        }
        if(file_exists('libraries/'.strtolower($folder).'/'.($name).'.lib.php')) return true;
        return false;
    }
    //Add symbiont
    public function addSymbiont($name){
	$isExisted=$this->symbionts->push($name);
        $this->includeSymbionts();
	return $isExisted;
    }
    //Add library
    public function addLibrary($name){
	$isExisted=$this->libraries->push($name);
        $this->includeLibraries();
	return $isExisted;
    }
    //Set language data
    public function setLanguage($abbr=''){
        global $db;
        if($abbr=='') $abbr=$kernel->link->language;
        $tmp=$db->select("languages", "*", array('abbr'=>$abbr), "", 1);
        $this->lang->add($tmp);
        $this->conf->base=$this->conf->url.($this->conf->abbreviations?$this->lang->abbr.'/':'');
    }
    public function __get($key){
	switch($key){
	    case 'conf': return $this->conf;
	    case 'page': return $this->page;
	    case 'link': return $this->link;
	    case 'lang': return $this->lang;
	    case 'vars': return $this->vars;
	}
    }
};
?>