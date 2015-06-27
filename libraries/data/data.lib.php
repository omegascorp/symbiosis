<?
//Data 0.2.9
class Data{
	//Defend from sql-injection
	static function safe($text, $len=0){
		$text=stripslashes($text);
		if($len>strlen($text)) $len=0;
		if($len){
			$nbsp=strpos($text, $symbols, $len);
			$text=($nbsp?substr($text, 0, $nbsp):substr($text, 0, $len)).'...';
		}
		$text=mysql_escape_string($text);
		return $text;
	}
	//Show HTML tags
	static function htmlView($text, $len=0){
		$text=htmlspecialchars(stripslashes($text));
		if($len>strlen($text)) $len=0;
		if($len){
			$nbsp=strpos($text, $symbols, $len);
			$text=($nbsp?substr($text, 0, $nbsp):substr($text, 0, $len)).'...';
		}
		$text=mysql_escape_string($text);
		return $text;
	}
	//Delete HTML tags
	static function htmlRemove($text, $len=0, $symbols=' '){
		$text=strip_tags(stripslashes($text));
		if($len>strlen($text)) $len=0;
		if($len){
			$nbsp=strpos($text, $symbols, $len);
			$text=($nbsp?substr($text, 0, $nbsp):substr($text, 0, $len)).'...';
		}
		$text=mysql_escape_string($text);
		return $text;
	}
	//P
	static function p($text, $count=1){
		$pos=strpos($text, '<p>');
		$len=strlen($text);
		$k=true;
		for($i=0; $i<$count; $i++){
			if($pos<$len){
				$pos=strpos($text, '<p>', $pos+3);
				if($pos==''){
					$k=false;
					break;
				}
			}
			else{
				$k=false;
				break;
			}
		}
		return $k?substr($text, 0, $pos):$text;
	}
	//Text to word
	static function word($text, $regexp='', $len=0, $addation=''){
		if($len) $text=substr($text, 0, $len);
		$text=str_replace(" ", "_", $text);
		$rep="";
		$json=json_decode(file_get_contents('libraries/data/config.json'));
		if(is_array($regexp)){
			foreach($regexp as $val){
				$rep.=$json->regexps->$val;
			}
		}
		elseif($regexp!=''){
			$rep.=$json->regexps->$regexp;
		}
		else{
			foreach($json->regexps as $key=>$val){
				$rep.=$val;
			}
		}
		$text=preg_replace("/[^".$addation.$rep."0-9]/", "", $text);
		return $text;
	}
	//Only file name
	static function fileName($text, $len=0){
		if($len) $text=substr($text, 0, $len);
		$text=str_replace(" ", "_", $text);
		$text=preg_replace("/[^A-z0-9_.-]/", "", $text);
		return $text;
	}
	//Free file
	static function fileFree($name, $dir='', $returnOnlyName=false){
		if(!$dir){
			$pos=strrpos($name, '/');
			$dir=substr($name, 0, $pos+1);
			$name=substr($name, $pos+1);
		}
		$right_name=Data::getFileName($name);
		$type=Data::getfileType($name);
		if(file_exists($dir.$right_name.".".$type)){
			$i=0;
			while(file_exists($dir.$right_name."_".$i.".".$type)){
				$i++;
			}
			$right_name.="_".$i;
		}
		return $returnOnlyName?$right_name.".".$type:$dir.$right_name.".".$type;
	}
	//Filename without type
	static function getFileName($text, $len=0){
		$text=Data::fileName($text, $len);
		$point=strrpos($text, ".");
		$text=substr($text, 0, $point);
		return $text;
	}
	//File system
	static function fileSystem($text, $top=false, $len=0){
		if($len) $text=substr($text, 0, $len);
		$text=str_replace(" ", "_", $text);
		$text=preg_replace("/[^A-z0-9_\.\-\/]/", "", $text);
		if(!$top) $text=str_replace('../', '', $text);
		return $text;
	}
	//Get folder content
	static function read($filename, $filter=null, $not=null){
		$folder=opendir($filename);
		$return=array();
		while($file=readdir($folder)){
		    if($file=='.'||$file=='..') continue;
		    if(($filter==null||preg_match($filter, $file))&&($not==null||!preg_match($not, $file))) array_push($return, $file);
		}
		return $return;
	}
	//Only e-mail
	static function email($text, $len=0){
		if($len) $text=substr($text, 0, $len);
		$text=preg_replace("/[^A-z0-9|_|@|.|-]/", "", $text);
		if(substr_count($text, "@")==1)	return $text;
		return 0;
	}
	//Only numbers
	static function number($numb){
		$numb=str_replace(",", ".", $numb);
		$numb=preg_replace("/[^0-9.\-+]/", "", $numb);
		if(substr_count($numb, ".")>1)	return "";
		return $numb;
	}
	//Only bool
	static function bool($bool){
		if(is_bool($bool)) return $bool;
		if($bool=="true") return true;
		if($bool=="false") return false;
		if($bool==0) return false;
		return true;
	}
	//Only list of numbers
	static function phones($numb){
		$numb=preg_replace("/[^0-9.\s,\-+]/", "", $numb);
		return $numb;
	}
	//Add file postfix
	static function addFilePostfix($file_name, $postfix){
		$v = strrpos($file_name, '.');
		$name = substr($file_name, 0, $v).$postfix.substr($file_name, $v);
		return $name;
	}
	//Create unical name
	static function getUniqName($name){
		$uniq = $this->uniq(12);
		$ext = strtolower(substr($name,strrpos($name,"."),strlen($name))); // RASSHIRENIE
		return $uniq.$ext;
	}
	//Generete unical random name
	static function uniq($n){
		return substr(uniqid(md5(microtime())),0,$n);
	}
	//Return the file type
	static function getFileType($fileName){
		$dot=strrpos($fileName, ".");
		return strtolower(substr($fileName, $dot+1));
	}
	//Return the name of symbiont
	static function symbiont($text){
		$text=preg_replace("/[^a-zA-Z0-9_|*\.\-\'\"=]/", "", $text);
		return $text;
	}
	//Remove all files in given folder and also the folder(recursive).
	static function clearFolder($str, $omit=null){
		if(is_file($str)){
		    return @unlink($str);
		}
		elseif(is_dir($str)){
		    $scan = glob(rtrim($str,'/').'/*');
		    foreach($scan as $index=>$path){
			$pos=strrpos($path, '/');
			$name=substr($path, $pos+1);
			if(is_array($omit)&&is_numeric(array_search($name, $omit))){
				continue;
			}
			elseif(is_string($omit)&&$omit==$name){
				continue;
			}
			Data::delete($path);
		    }
		}
	}
	//Remove all files in given folder and also the folder(recursive).
	static function delete($path){
		if(is_file($path)){
		    return @unlink($path);
		}
		elseif(is_dir($path)){
		    $path=rtrim($path,'/');
		    $dir=opendir($path);
		    while($file=readdir($dir)){
			if($file!='.'&&$file!='..'){
				Data::delete($path.'/'.$file);
			}
		    }
		    closedir($dir);
		    return @rmdir($path);
		}
	}
	//Create folder
	static function createFolder($path){
		if(!is_dir($path)){
		    umask(0000);
		    return mkdir($path, 0777, true);
		}
		return 0;
	}
	static function urlExists($url){
		$r=true;
		preg_match("/http:\/\/([^\/]*)(.*)/", $url, $matches);
		$site=$matches[1];
		$page=$matches[2];
		$sock = fsockopen($site, 80, $en, $es, 10);
		if (!is_resource($sock)) $r=false;
		fwrite($sock, "HEAD $page HTTP/1.0\r\n\r\n");
		$content=fgets($sock, 256);
		if(preg_match("/.*(403|404).*/", $content)){
			$r=false;
		}
		fclose($sock);
		return $r;
	}
	//Is int
	static function isInt($val){
		if(is_int($val)) return true;
		if($val==='0') return true;
		if(preg_match("/^-?[1-9][0-9]*$/", $val)){
			return true;
		}
		return false;
	}
	//Is unsigned int
	static function isUint($val){
		if(is_int($val)){
			if($val>=0) return true;
			return false;
		}
		if($val==='0') return true;
		if(preg_match("/^[1-9][0-9]*$/", $val)){
			return true;
		}
		return false;
	}
	//Is real
	static function isReal($val){
		if(is_double($val)) return true;
		if(preg_match("/^-?(([1-9][0-9]*)|0)((\.)[0-9]*)?$/", $val)){
			return true;
		}
	}
	//Is unsigned real
	static function isUreal($val){
		if(is_double($val)) return true;
		if(preg_match("/^(([1-9][0-9]*)|0)((\.)[0-9]*)?$/", $val)){
			return true;
		}
	}
	//Is bool
	static function isBool($val){
		if(is_bool($val)) return true;
		if($val=="true"||$val=="false") return true;
		return false;
	}
	//Is e-mail
	static function isEmail($email){
		if(preg_match("/[0-9a-z_]+@[0-9a-z_^\.]+\.[a-z]{2,3}/i", $email)){
			return true;
		}
		return false;
	}
	static function extend($first, $second){
		if(!is_array($first)) $first=array();
		if(is_array($second)){
			foreach($second as $key=>$val){
				$first[$key]=$val;
			}
		}
		return $first;
	}
}
?>