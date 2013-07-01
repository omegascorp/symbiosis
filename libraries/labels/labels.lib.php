<?
//Labels 0.0.3
class Labels extends Dictionary{
    private $loaded;
    public function __construct(){
        $this->loaded=new Set();
    }
    public function import($fileName){
        global $kernel;
        if(!$kernel->lang->abbr) return;
        if(substr($fileName, -1, 1)!='/') $fileName.='.';
        $fileName.=$kernel->lang->abbr.'.json';
        if(!file_exists($fileName)) return;
        if(!$this->loaded->push($fileName)) return;
        $text=file_get_contents($fileName);
        $json=json_decode($text);
        if(is_object($json))
        foreach($json as $key=>$val){
            $this->set($key, $val);
        }
    }
    public function grab($key, $vars=null){
        $text=parent::get($key);
        if(is_array($vars)){
            preg_match_all("/\{\&([a-zA-Z0-9_]*)((\[[a-zA-Z0-9_]*\])*)\}/", $text, $matches);
            $i=0;
            foreach($matches[0] as $old){
                $var=$matches[1][$i];
                if(isset($vars[$var])){
                    $var=$vars[$var];
                }
                else{
                    continue;
                }
                $indexes=explode('][', substr($matches[2][$i], 1, -1));
                foreach($indexes as $index){
                    if(isset($vars[$index])){
                        $var=$vars[$index];
                    }
                    else{
                        break;
                    }
                }
                $text=str_replace($old, $var, $text);
                ++$i;
            }
        }
        return $text;
    }
}
?>