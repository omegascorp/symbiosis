<?
class SymbiontInfo{
    private $full;
    private $symbiont;
    private $class;
    private $symbiontAndClass;
    private $symbiontClass;
    private $function;
    private $attributes;
    private $template;
    private $content;
    public function __construct($string=null, $version=1){
        $this->full=$string;
        $this->symbiont='';
        $this->class='';
        $this->symbiontAndClass='';
	$this->symbiontClass='';
        $this->function='main';
        $this->attributes=array();
        $this->template='';
        $this->content='';
        if($string){
	    if($version==2){
		$this->explode2($string);
	    }
	    else{
		$this->explode($string);
	    }
        }
    }
    public function explode($string){
        $i=0;
	$last=null;
	$union=false;
	
	if(substr_count($string, '|')){
	    list($symbiont, $this->template)=explode('|', $string);
	}
	else{
	    $symbiont=$string;
	    $this->template='';
	}
	
	$words=explode('.', $symbiont);
	
	if(isset($words[0])){
	    $this->symbiontAndClass=$words[0];
	    if(substr_count($words[0], '-')){
		list($this->symbiont, $this->class)=explode("-", $words[0]);
	    }
	    else{
		$this->symbiont=$words[0];
		$this->class='';
	    }
	    $this->symbiontClass=$this->symbiont.$this->class;
	    unset($words[0]);
	}
	if(isset($words[1])){
	    $this->function=$words[1];
	    unset($words[1]);
	}
	foreach($words as $current){
	    if($union) $key=$last;
	    
	    $p=strpos($current, '=');
	    if($p){
		if(!$union) $key=substr($current, 0, $p);
		$val=substr($current, $p+1);
	    }
	    else{
		if(!$union) $key=$i++;
		$val=$current;
	    }
	    //$val=$design->varsLocal($val);
	    $count=substr_count($val, "'");
	    if($count==0) $count=substr_count($val, '"');
	    if($count==2){
		$val=substr($val, 1, -1);
	    }
	    elseif($count==1){
		$len=strlen($val)-1;
		$first=substr($val, 0, 1);
		if($first=="'"||$first=='"'){
		    $union=true;
		    $val=substr($val, 1);
		}
		$last=substr($val, $len, 1);
		if($last=="'"||$last=='"'){
		    $union=false;
		    $val=substr($val, 0, $len);
		}
	    }
	    if(isset($this->attributes[$key])){
		$this->attributes[$key].='.'.$val;
	    }
	    else{
		$this->attributes[$key]=$val;
	    }
	    $last=$key;
	}
    }
    public function explode2($string){
	$i=0;
	$last=null;
	$union=false;
	
	if(strpos($string, '[')!=false){
	    $symbiont=substr($string, 0, strpos($string, '['));
	    $attrs=substr($string, strpos($string, '[')+1, strrpos($string, ']')-strpos($string, '[')-1);
	}
	else{
	    $symbiont=$string;
	    $attrs=null;
	}
        $words=explode('.', $symbiont);
	
	if(isset($words[0])){
	    $this->symbiontAndClass=$words[0];
	    if(substr_count($words[0], '-')){
		list($this->symbiont, $this->class)=explode("-", $words[0]);
	    }
	    else{
		$this->symbiont=$words[0];
		$this->class='';
	    }
	    $this->symbiontClass=$this->symbiont.$this->class;
	}
	if(isset($words[1])){
	    $this->function=$words[1];
	}
	if(isset($words[2])){
	    $this->template=$words[2];
	}
	
	if($attrs!=null){
	    $words=explode(',', $attrs);
	    foreach($words as $current){
		if($union) $key=$last;
		
		$p=strpos($current, '=');
		if($p){
		    if(!$union) $key=substr($current, 0, $p);
		    $val=substr($current, $p+1);
		}
		else{
		    if(!$union) $key=$i++;
		    $val=$current;
		}
		//$val=$design->varsLocal($val);
		$count=substr_count($val, "'");
		if($count==0) $count=substr_count($val, '"');
		if($count==2){
		    $val=substr($val, 1, -1);
		}
		elseif($count==1){
		    $len=strlen($val)-1;
		    $first=substr($val, 0, 1);
		    if($first=="'"||$first=='"'){
			$union=true;
			$val=substr($val, 1);
		    }
		    $last=substr($val, $len, 1);
		    if($last=="'"||$last=='"'){
			$union=false;
			$val=substr($val, 0, $len);
		    }
		}
		if(isset($this->attributes[$key])){
		    $this->attributes[$key].=','.$val;
		}
		else{
		    $this->attributes[$key]=$val;
		}
		$last=$key;
	    }
	    if(isset($this->attributes['template'])) $this->template=$this->attributes['template'];
	}
    }
    public function __get($key){
        return $this->$key;
    }
    public function __set($key, $val){
        $this->$key=$val;
    }
}
?>