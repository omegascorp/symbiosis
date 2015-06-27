<?
//Place 0.0.2
class Place{
    static public $places=array();
    static public function init(){
        global $kernel, $labels;
        $kernel->vars->title=$labels->get('title');
        Place::push($kernel->page->title, $kernel->page->alias);
    }
    static public function push($title, $alias=null, $shortlink=null){
        global $kernel;
        array_push(Place::$places, array('title'=>$title, 'alias'=>$alias, 'shortlink'=>$shortlink));
        
        if($kernel->vars->title) $kernel->vars->title.=' | ';
        $kernel->vars->title.=$title;
        
        $kernel->vars->place='';
        $last=count(Place::$places)-1;
        $path=$kernel->conf->base;
        foreach(Place::$places as $key=>$place){
            if($kernel->vars->place) $kernel->vars->place.=' → ';
            if($place['shortlink']){
                $path=''; 
            }
            if($place["alias"]){
                $path.=$place["alias"].'/';
            }
            if($place["alias"]&&$key!=$last){
                $kernel->vars->place.='<a href="'.$path.'">'.$place["title"].'</a>';
            }
            else{
                $kernel->vars->place.=$title;
            }
        }
    }
    static public function clear(){
        global $kernel;
        $kernel->vars->place='';
        $kernel->vars->title='';
        Place::$places=array();
    }
}
?>