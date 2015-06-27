<?
class SComments extends Symbiont{
    private $templateComment;
    public function main($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design, $user;
        $attributes=Data::extend(array(
            'for'=>''
        ), $attributes);
        $template=$this->_check($template, 'main');
        $vars=array();
        $vars['for']=$attributes['for'];
        $vars['autor']=$comments=$db->query('
            SELECT CONCAT(u.firstName, " ", u.lastName) as autor
            FROM `users` as u
            WHERE id='.$user->id.'
            LIMIT 1
        ', true);
        $template=$design->show($template, $vars);
    }
    public function getComments($template=null, $attributes=null, $content=null){
        global $db, $kernel, $design;
        
        $attributes=Data::extend(array(
            'parentId'=>0,
            'level'=>0,
            'for'=>''
        ),$attributes);
        $for=Data::safe($attributes['for']);
        $parentId=Data::number($attributes['parentId']);
        
        $comments=$db->query('
            SELECT c.id, c.text, c.date, CONCAT(u.firstName, " ", u.lastName) as autor,
                (SELECT 1 FROM `scomments` WHERE parentId=c.id LIMIT 1) as sub
            FROM `scomments` as c
                LEFT JOIN `users` as u
                    ON u.id=c.userId
            WHERE c.for="'.$for.'" AND parentId='.$parentId.'
        ');
        
        $templateComments='';
        if(is_array($comments)){
            foreach($comments as $comment){
                $comment['answers']='';
                if($comment['sub']){
                    $attributes['parentId']=$comment['id'];
                    $comment['answers']=$this->getComments($template, $attributes, $comment);
                }
                $templateComments.=$design->run($this->templateComment, $comment);
            }
        }
        return $templateComments;
    }
    public function setComment($template=null, $attributes=null, $content=null){
        $this->templateComment=$content;
    }
    public function dbChange($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        if($user->accessLevel<1){
            
            return '{"error":"'.$labels->get('errors.prerogatives').'"}';
        }
        if(!isset($_POST['text'])||!isset($_POST['for'])){
            
            return '{"error":"'.$labels->get('errors.parametrs').'"}';
        }
        
        $values=array();
        $values['languageId']=$kernel->lang->id;
        $values['parentId']=isset($_POST['parentId'])?Data::number($_POST['parentId']):0;
        $values['text']=Data::htmlView($_POST['text']);
        $values['for']=Data::safe($_POST['for']);
        $values['userId']=$user->id;
        
        
        if(isset($_POST['id'])){
            $where=array();
            $where['id']=Data::number($_POST['id']);
            $r=$db->update('scomments', $values, $where);
        }
        else{
            $r=$db->insert('scomments', $values);
        }
        if($r){
            $date=$db->query('SELECT date FROM `scomments` WHERE id='.$r.' LIMIT 1', true);
            return '{"success":"","id":"'.$r.'","date":"'.$date.'","text":"'.$values['text'].'"}';
        }
        else{
            return '{"error":""}';
        }
    }
    public function dbDelete($template=null, $attributes=null, $content=null){
        global $user, $db, $kernel, $design, $labels;
        if($user->accessLevel<9){
            
            return '{"error":"'.$labels->get('errors.prerogatives').'"}';
        }
        if(!isset($_POST['id'])){
            
            return '{"error":"'.$labels->get('errors.parametrs').'"}';
        }
    }
}
?>