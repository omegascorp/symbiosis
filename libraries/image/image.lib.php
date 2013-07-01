<?
//Image library 0.0.15
class Image{
    private $name;      //File name
    private $dir;       //File directory
    private $path;      //Image path
    private $isImage;   //Is path valid
    private $width;		//Current width
    private $height;		//Current height
    public function __construct($path){
        $slash=strrpos($path, "/");
        $dot=strrpos($path, ".");
        $this->dir=substr($path, 0, $slash+1);
        $this->name=substr($path, $slash+1, $dot-$slash-1);
        $this->type=strtolower(substr($path, $dot+1));
        $this->path=$path;
	if(file_exists($path)&&($this->type=="jpg"||$this->type=="jpeg"||$this->type=="gif"||$this->type=="png"||$this->type=="bmp")){
            list($this->width,$this->height,$t) = getimagesize($path);
            $this->isImage=true;
        }
        else{
            $this->isImage=false;
        }
    }
    //Resize JPG
    private function resizeJPG($path, $width, $height, $x1, $x2, $y1, $y2, $quality=80){
	$source=@imagecreatefromjpeg($this->path);
	if(!$source) return false;
	$receiver=ImageCreateTrueColor($width,$height);
	imagecopyresampled($receiver,$source,0,0,$x1,$y1,$width,$height,$x2-$x1,$y2-$y1);
	$ret=imagejpeg($receiver,$path, $quality);
	imagedestroy($receiver);
	imagedestroy($source);
	return $ret;
    }
    //Resize GIF
    private function resizeGIF($path, $width, $height, $x1, $x2, $y1, $y2){
	$source=@imagecreatefromgif($this->path);
	if(!$source) return false;
	$receiver=ImageCreateTrueColor($width,$height);
	imagecopyresampled($receiver,$source,0,0,$x1,$y1,$width,$height,$x2-$x1,$y2-$y1);
	$ret=imagegif($receiver,$path);
	imagedestroy($receiver);
	imagedestroy($source);
	return $ret;
    }
    //Resize PNG
    private function resizePNG($path, $width, $height, $x1, $x2, $y1, $y2){
	$source=@imagecreatefrompng($this->path);
	if(!$source) return false;
	$receiver=ImageCreateTrueColor($width,$height);
	imagealphablending($receiver, false);
	imagesavealpha($receiver, true);
	imagecopyresampled($receiver,$source,0,0,$x1,$y1,$width,$height,$x2-$x1,$y2-$y1);
	$ret=imagepng($receiver,$path);
	imagedestroy($receiver);
	imagedestroy($source);
	return $ret;
    }
    //Resize BMP
    private function resizeBMP($path, $width, $height, $x1, $x2, $y1, $y2){
	$source=@imagecreatefrombmp($this->path);
	if(!$source) return false;
	$receiver=ImageCreateTrueColor($width,$height);
	imagecopyresampled($receiver,$source,0,0,$x1,$y1,$width,$height,$x2-$x1,$y2-$y1);
	$ret=imagebmp($receiver,$path);
	imagedestroy($receiver);
	imagedestroy($source);
	return $ret;
    }
    public function resize($name, $width=null, $height=null, $fixed=false, $rewrite=false){
	if(!$this->isImage) return null;
	/*
        if(isset($postfix[0])&&$postfix[0]=='/'){
	    Data::createFolder($this->dir.substr($postfix, 1));
	    $this->pathNew=$this->dir.substr($postfix, 1).$this->name.".".$this->type;
	}
	else{
	    $this->pathNew=$this->dir.$this->name.$postfix.".".$this->type;
	}
	*/
	
	$name=str_replace('*', $this->name.'.'.$this->type, $name);
	$name=str_replace('{name}', $this->name, $name);
	$name=str_replace('{type}', $this->type, $name);
	if(substr($name,0,1)=='/') $name=substr($name,1);
	$this->pathNew=$this->dir.$name;
	$slash=strrpos($this->pathNew, '/');
	if($slash!=-1){
	    Data::createFolder(substr($this->pathNew, 0, $slash));
	}
        if(!$rewrite){
            $this->pathNew=Data::fileFree($this->pathNew);
	}
        if($fixed||$width!=null&&$this->width>$width||$height!=null&&$this->height>$height){
	    $x1=0;
	    $y1=0;
	    $x2=$this->width;
	    $y2=$this->height;
	    if($width==null){
		$h=$height/$this->height;
		$width=$h*$this->width;
	    }
	    elseif($height==null){
		$w=$width/$this->width;
		$height=$w*$this->height;
	    }
	    else{
		$w=$width/$this->width;
		$h=$height/$this->height;
		if($w>=$h){
		    if($fixed){
			$margin=($this->height-$height/$w)/2;
			$y1=$margin;
			$y2-=$margin;
		    }
		    else{
			$width=$h*$this->width;
		    }
		}
		else{
		    if($fixed){
			$margin=($this->width-$width/$h)/2;
			$x1=$margin;
			$x2-=$margin;
		    }
		    else{
			$height=$w*$this->height;
		    }
		}
	    }
	    if($this->type=="jpg"||$this->type=="jpeg") $ret=$this->resizeJPG($this->pathNew, $width, $height, $x1, $x2, $y1, $y2);
	    elseif($this->type=="gif") $ret=$this->resizeGIF($this->pathNew, $width, $height, $x1, $x2, $y1, $y2);
	    elseif($this->type=="png") $ret=$this->resizePNG($this->pathNew, $width, $height, $x1, $x2, $y1, $y2);
	    elseif($this->type=="bmp") $ret=$this->resizeBMP($this->pathNew, $width, $height, $x1, $x2, $y1, $y2);
	    return $this->pathNew;
        }
	if($this->path!=$this->pathNew){
	    copy($this->path, $this->pathNew);
	}
	return $this->pathNew;
    }
    public function poster($name, $width, $height, $x1, $x2, $y1, $y2, $rewrite=false){
	$name=str_replace('*', $this->name.'.'.$this->type, $name);
	$name=str_replace('{name}', $this->name, $name);
	$name=str_replace('{type}', $this->type, $name);
	$this->pathNew=$this->dir.$name;
	$slash=strrpos($this->pathNew, '/');
	if($slash!=-1){
	    Data::createFolder(substr($this->pathNew, 0, $slash));
	}
        if(!$rewrite){
            $this->pathNew=Data::fileFree($this->pathNew);
	}
	
	if($this->type=="jpg"||$this->type=="jpeg") $ret=$this->resizeJPG($this->pathNew, $width, $height, $x1, $x2, $y1, $y2);
	elseif($this->type=="gif") $ret=$this->resizeGIF($this->pathNew, $width, $height, $x1, $x2, $y1, $y2);
	elseif($this->type=="png") $ret=$this->resizePNG($this->pathNew, $width, $height, $x1, $x2, $y1, $y2);
	elseif($this->type=="bmp") $ret=$this->resizeBMP($this->pathNew, $width, $height, $x1, $x2, $y1, $y2);
	return $this->pathNew;
    }
    public function __set($key, $val){
	$this->$key=$val;
    }
    public function __get($key){
	return $this->$key;
    }
}
?>