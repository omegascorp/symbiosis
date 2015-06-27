<?
class Design{
    public function init(){
	global $design;
	ob_start();
    }
    //Sow template
    public function show($designName, $vars='', $functions=''){
	global $kernel, $symbionts, $user, $labels;
	$design='!'.$designName.'.des.html';
	$template='temp/'.$designName.'.des.html';
	if(file_exists($template)){
	    include($template);
	}
	else{
	    if(file_exists($design)){
		$content=file_get_contents($design);
	    }
	    else{
		$content="";
	    }
	    
	    $pos=strrpos($designName, '/');
	    $folder=substr($designName, 0, $pos);
	    Data::createFolder('temp/'.$folder);
	    
	    $content=$this->markup($content);
	    $content=str_replace("\v", "", $content);
	    
	    eval('?>'.$content.'<?');
	    
	    $file=fopen($template, "w");
	    fwrite($file, $content);
	    fclose($file);
	}
    }
    public function take($designName, $vars='', $functions=''){
	ob_start();
	$this->show($designName, $vars, $functions);
	return ob_get_clean();
    }
    public function destroy(){
	global $kernel;
	
	$content=ob_get_clean();
	$content=$this->varsFinish($content);
	print $content;
	
	if($kernel->conf->regenerate){
	    Data::clearFolder('temp/', 'important');
	}
    }
    //Processing markup CEML (Control Elements Markup Language)
    private function markup($text){
	$text=str_replace("\{", "[lb]", $text);
	$text=str_replace("\}", "[rb]", $text);
	$text=$this->comments($text);
	$text=$this->XML($text);
	$text=$this->ifs($text);
	$text=$this->each($text);
	$text=$this->fors($text);
	$text=$this->repeat($text);
	$text=$this->symbiontsNew($text);
	$text=$this->positionsDynamic($text);
	$text=$this->symbiontsContainer($text);
	$text=$this->symbionts($text);
	$text=$this->positions($text);
	$text=$this->labels($text);
	$text=$this->expressions($text);
	$text=$this->after($text);
	$text=str_replace("[lb]", "{", $text);
	$text=str_replace("[rb]", "}", $text);
	return $text;
    }
    private function after($text){
	$text=str_replace('\{', '{', $text);
	$text=str_replace('\}', '}', $text);
        return $text;
    }
    private function expressions($text){
        preg_match_all("/\{([^\v\\\]*)\}/Us", $text, $matches);
	$i=-1;
	foreach($matches[0] as $old){
	    ++$i;
	    $exp=$matches[1][$i];
	    $print=true;
	    if($exp[0]=='#'){
		$print=false;
		$exp=substr($exp, 1);
	    }
	    if(substr($exp, 0, 2)=='$$') continue;
	    $exp=$this->vars($exp);
            $exp=$this->varsLocal($exp);
	    $new="<?".($print?"=":"").$exp.($print?"":";")."?>";
	    $text=str_replace($old, $new, $text);
	}
        return $text;
    }
    private function vars($text){
	preg_match_all("/\\\$([a-zA-Z0-9_\.]*)/", $text, $matches);
        $i=0;
        foreach($matches[0] as $old){
            $var=explode('.', $matches[1][$i]);
	    if(count($var)==1){
		$new='$kernel->vars->'.$var[0];
	    }
	    else{
		$new='$kernel->'.$var[0].'->'.$var[1];
	    }
            $text=str_replace($old, $new, $text);
            ++$i;
        }
	return $text;
    }
    private function varsFinish($text){
	global $kernel;
	preg_match_all("/\{\\\$\\\$([a-zA-Z0-9_\.]*)\}/", $text, $matches);
        $i=0;
        foreach($matches[0] as $old){
            $var=explode('.', $matches[1][$i]);
	    if(count($var)==1){
		$new=$kernel->vars->$var[0];
	    }
	    else{
		$new=$kernel->$var[0]->$var[1];
	    }
            $text=str_replace($old, $new, $text);
            ++$i;
        }
	return $text;
    }
    private function varsLocal($text){
        preg_match_all("/\&([a-zA-Z0-9_]+)((\[[a-zA-Z0-9_]*\])*)/", $text, $matches);
        $i=0;
        foreach($matches[0] as $old){
            $var=$matches[1][$i];
            $new='$vars["'.$var.'"]';
	    $indexes=explode('][', substr($matches[2][$i], 1, -1));
	    foreach($indexes as $index){
		if($index!=='') $new .= is_numeric($index)?'['.$index.']':'["'.$index.'"]';
	    }
            $text=str_replace($old, $new, $text);
            ++$i;
        }
	return $text;
    }
    //Processing labels
    private function labels($text){
	global $labels;
        preg_match_all("/\{@(@)?([a-zA-Z0-9_\.-]*)(\|([a-zA-Z0-9]*))?\}/", $text, $matches);
        $i=0;
        foreach($matches[0] as $old){
	    $useVars=$matches[1][$i];
            $word=$matches[2][$i];
            if($labels->exists($word)){
		$vars=$useVars?", \$vars":'';
		if($matches[3][$i]){
		    switch($matches[3][$i]){
			case 'lower': $function='strtolower'; break;
			case 'upper': $function='strtoupper'; break;
			case 'first': $function='ucfirst'; break;
			case 'word': $function='ucwords'; break;
			default: $function=''; break;
		    }
		    $new="\v<?=(".$function."(\$labels->".($useVars?"grab":"get")."('".$word."'".$vars.")))?>\v";
		}
		else{
		    $new="\v<?=(\$labels->".($useVars?"grab":"get")."('".$word."'".$vars."))?>\v";
		}
            }
            else{
                $new='@'.$word;
            }
            $text=str_replace($old, $new, $text);
            ++$i;
        }
	return $text;
    }
    //Processing positions
    private function positions($text=""){
        global $kernel, $db;
        preg_match_all("/{\[([a-zA-Z0-9_.]*)\]}/", $text, $matches);
        $i=0;
        foreach($matches[0] as $old){
            $alias=$matches[1][$i];
            $new="\v<?\$p=new Positions('".$alias."');\$p->read();\$p->show();?>\v";
            $text=str_replace($old, $new, $text);
            ++$i;
        }
        return $text;
    }
    //Processing dynamic positions
    private function positionsDynamic($text=""){
        global $kernel;
        preg_match_all("/\{([a-zA-Z0-9_.]*)\{([a-zA-Z0-9_\.]*)\}([a-zA-Z0-9_.]*)\}/", $text, $matches);
        $i=0;
        foreach($matches[0] as $old){
            $prefix=$matches[1][$i];
            $suffix=$matches[3][$i];
	    $var=explode('.', $matches[2][$i]);
	    if(count($var)==1){
		$name='$kernel->vars->'.$var[0];
	    }
	    else{
		$name='$kernel->'.$var[0].'->'.$var[1];
	    }
            $new="\v<?\$p=new Positions('".$prefix."'.".$name.".'".$suffix."');\$p->read();\$p->show();?>\v";
            $text=str_replace($old, $new, $text);
            ++$i;
        }
        return $text;
    }
    private function comments($text){
        $text=preg_replace("/\{\*(.*)\*}/Us", "", $text);
        return $text;
    }
    //Alow XML
    private function XML($text=''){
	preg_match_all("/(<\?xml (.*)\?>)/Us", $text, $matches);
	$i=0;
	foreach($matches[0] as $old){
	    $new="\v<?='".addslashes($old)."'?>\v";
	    $text=str_replace($old, $new, $text);
	    $i++;
	}
	return $text;
    }
    //Processing symbionts
    private function symbiontsContainer($text=""){
        global $kernel;
        preg_match_all("/\{(([a-zA-Z0-9_]*(-[a-zA-Z0-9_]*)?)(\.[a-zA-Z0-9_]*)?(\.[a-zA-Z0-9_]*(\=[&a-zA-Z0-9_\'\"\ ]*))*(\|[a-zA-Z0-9_]*)?)\}(.*){\/\\2}/Us", $text, $matches);
        $i=0;
        foreach($matches[0] as $old){
            $word=$matches[1][$i];
            $symbiont=new SymbiontInfo($word);
	    $symbiont->content=$matches[8][$i];
	    $symbiont->content=$this->markup($symbiont->content);
	    $symbiont->content=str_replace("'", "\'", $symbiont->content);
	    if($kernel->isExistsSymbiont($symbiont->symbiont)){
		$new="\v<?".Design::symbiont($symbiont)."?>\v";
	    }
	    else{
		$new=""; //Symbiont not exists;
	    }
            $text=str_replace($old, $new, $text);
	    ++$i;
        }
        return $text;
    }
    //Processing ifs
    private function ifs($text=""){
        //if
        preg_match_all("/\{if\(([a-zA-Z0-9_~\-., ><=!\&\#\$\'\"\|\[\]\(\)\/]*)\)\}/", $text, $matches);
        $i=0;
        foreach($matches[0] as $old){
            $ext=$matches[1][$i];
            $ext=$this->vars($ext);
            $ext=$this->varsLocal($ext, false);
            $ext=str_replace("~", "\$user->accessLevel", $ext);
            $new="\v<?if(".$ext."){?>\v";
            $text=str_replace($old, $new, $text);
            ++$i;
        }
        //elseif
        preg_match_all("/\{elseif\(([a-zA-Z0-9_~\-. ><=!\&\#\$\'\"\|\[\]\/]*)\)\}/", $text, $matches);
        $i=0;
        foreach($matches[0] as $old){
            $ext=$matches[1][$i];
            $ext=$this->vars($ext);
            $ext=$this->varsLocal($ext);
            $ext=str_replace("~", "\$user->accessLevel", $ext);
            $new="\v<?}elseif(".$ext."){?>\v";
            $text=str_replace($old, $new, $text);
            ++$i;
        }
        //else
        preg_match_all("/\{else\}/", $text, $matches);
        $i=0;
        foreach($matches[0] as $old){
            $new="\v<?}else{?>\v";
            $text=str_replace($old, $new, $text);
            ++$i;
        }
        ///if
        preg_match_all("/\{\/if\}/", $text, $matches);
        $i=0;
        foreach($matches[0] as $old){
            $new="\v<?}?>\v";
            $text=str_replace($old, $new, $text);
            ++$i;
        }
	
	return $text;
    }
    private function each($text){
	preg_match_all("/\{each\((\&[a-zA-Z0-9\[\]]*) as ((\&[a-zA-Z0-9\[\]]*) ?=> ?)?(\&[a-zA-Z0-9\[\]]*)\)\}/", $text, $matches);
	$i=0;
        foreach($matches[0] as $old){
	    $var=$this->varsLocal($matches[1][$i]);
	    $key=$this->varsLocal($matches[3][$i]);
            $as=$this->varsLocal($matches[4][$i]);
	    if($key) $as=$key.'=>'.$as;
            $new="\v<?if(is_array(".$var.")){foreach(".$var." as ".$as."){?>\v";
            $text=str_replace($old, $new, $text);
            ++$i;
        }
	$text=str_replace("{/each}", "\v<?}}?>\v", $text);
        return $text;
    }
    //For
    private function fors($text){
	preg_match_all("/\{for\(([a-zA-Z0-9\[\]\&\ ;=+-\/\(\)\*<>]*)\)\}/", $text, $matches);
	$i=0;
        foreach($matches[0] as $old){
	    $head=$this->varsLocal($matches[1][$i]);
            $new="\v<?for(".$head."){?>\v";
            $text=str_replace($old, $new, $text);
            ++$i;
        }
	$text=str_replace("{/for}", "\v<?}?>\v", $text);
        return $text;
    }
    //Repeat
    private function repeat($text){
	preg_match_all("/\{repeat\(([a-zA-Z0-9\+\-\/\*\&]*)\)\}/", $text, $matches);
	$i=0;
        foreach($matches[0] as $old){
	    $count=$this->varsLocal($matches[1][$i]);
            $new="\v<?for(\$i=0;\$i<".$count.";\$i++){?>\v";
            $text=str_replace($old, $new, $text);
            ++$i;
        }
	$text=str_replace("{/repeat}", "\v<?}?>\v", $text);
        return $text;
    }
    //Processing symbionts
    private function symbionts($text=""){
        global $kernel;
        preg_match_all("/\{([a-zA-Z0-9_]*(-[a-zA-Z0-9_]*)?(\.[a-zA-Z0-9_]*)?(\.[a-zA-Z0-9_]*(\=[&a-zA-Z0-9_\'\"\ \.\[\]\+\-\/\*]*))*(\|[&a-zA-Z0-9_-]*)?)(\/)?\}/", $text, $matches);
	//([a-zA-Z0-9_\.\-\"\'\=\| ]*)
        $i=0;
        foreach($matches[0] as $old){
            $word=$matches[1][$i];
            $symbiont=new SymbiontInfo($word);
	    if($kernel->isExistsSymbiont($symbiont->symbiont)){
		$new=str_replace("\v", "", Design::symbiont($symbiont));
		$new="\v<?".$new."?>\v";
	    }
	    else{
		$new=""; //Symbiont not exists;
	    }
            $text=str_replace($old, $new, $text);
	    ++$i;
        }
        return $text;
    }
    //Processing new style symbionts
    private function symbiontsNew($text=""){
        global $kernel;
        preg_match_all("/\{#([a-zA-Z0-9_]*(-[a-zA-Z0-9_]*)?(\.[&a-zA-Z0-9_-]*)?(\.[&a-zA-Z0-9_-]*)?(\[.*\])?)#\}/", $text, $matches);
        $i=0;
        foreach($matches[0] as $old){
            $word=$matches[1][$i];
            $symbiont=new SymbiontInfo($word, 2);
	    if($kernel->isExistsSymbiont($symbiont->symbiont)){
		$new=str_replace("\v", "", Design::symbiont($symbiont, 2));
		$new="\v<?".$new."?>\v";
	    }
	    else{
		$new=""; //Symbiont not exists;
	    }
            $text=str_replace($old, $new, $text);
	    ++$i;
        }
        return $text;
    }
    public static function symbiont($symbiont, $version=1){
	global $design;
	if(is_string($symbiont)) $symbiont=new SymbiontInfo($symbiont, $version);
	if($symbiont->symbiont=='') return;
	
	if(!$symbiont->class){
	    $ret='$kernel->addSymbiont("'.$symbiont->symbiont.'");';
	    $class=$symbiont->symbiont;
	    
	}
	else{
	    $ret='$kernel->addSymbiont("'.$symbiont->symbiontAndClass.'");';
	    $class=$symbiont->symbiont.$symbiont->class;
	}
	
	$function=$symbiont->function;
	$attributes='array(';
	$coma=false;
	foreach($symbiont->attributes as $key=>$val){
	    if($coma) $attributes.=', ';
	    $attributes.="'".$key."' => ";
	    if($val==''){
		$attributes.="''";
	    }
	    elseif(Data::isBool($val)||Data::isReal($val)||substr($val, 0, 1)=="&"){
		$attributes.=$val;
	    }
	    else{
		$attributes.="'".$val."'";
	    }
	    $coma=true;
	}
	$attributes.=")";
	$attributes=$design->varsLocal($attributes);
	$template=$symbiont->template;
	if(substr_count($template, '&')!=0){
	    $template=$design->varsLocal($template);
	}
	else{
	    $template='"'.$template.'"';
	}
	$content=$symbiont->content;
	$ret.='if(isset($symbionts->'.$class.')) print($symbionts->'.$class.'->'.$function.'('.$template.', '.$attributes.', \''.$content.'\'));';
	return $ret;
    }
    public static function symbiontInclude($symbiont){
	global $design, $kernel, $symbionts;
	if(is_string($symbiont)){
	    if(substr($symbiont,0,1)=="#"){
		$symbiont=substr($symbiont,1);
		$version=2;
	    }
	    else{
		$version=1;
	    }
	    $symbiont=new SymbiontInfo($symbiont, $version);
	}
	if($symbiont->symbiont=='') return;
	
	if(!$symbiont->class){
	    $kernel->addSymbiont($symbiont->symbiont);
	    $class=$symbiont->symbiont;
	}
	else{
	    $kernel->addSymbiont($symbiont->symbiontAndClass);
	    $class=$symbiont->symbiont.$symbiont->class;
	}
	$template=$symbiont->template;
	$content=$symbiont->content;
	$function=$symbiont->function;
	$attributes=$symbiont->attributes;
	$symbionts->$class->$function($template, $attributes, $content);
    }
    public static function symbiontGet($symbiont){
	ob_start();
	Design::symbiontInclude($symbiont);
	return ob_get_clean();
    }
    public static function symbiontEval($symbiont, $version=1){
	global $kernel, $symbionts;
	if(substr($symbiont, 0, 1)=="#"){
	    $s=Design::symbiont(substr($symbiont, 1), 2);
	}
	elseif($version==2){
	    $s=Design::symbiont($symbiont, 2);
	}
	else{
	    $s=Design::symbiont($symbiont);
	}
	
	eval($s);
    }
    public static function symbiontExplode($string){
	return new SymbiontInfo($string);
    }
    public function __get($key){
	switch($key){
	    case 'styleTags': return 0;
	    case 'styleQuots': return 1;
	    case 'styleNothing': return 2;
	}
    }
    public function run($template, $vars=''){
	global $kernel, $symbionts, $labels;
	ob_start();
	$template=str_replace("\v", "", $template);
	eval('?>'.$template.'<?');
	return ob_get_clean();
    }
};
?>