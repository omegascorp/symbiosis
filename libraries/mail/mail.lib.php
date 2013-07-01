<?
//Mail 0.0.5
class Mail{
    private $to = '';
    private $subject = '';
    private $msg = '';
    private $headers = '';
    //Create message
    public function __construct($to, $fromName, $from, $subject, $msg, $file='', $cc='', $bcc=''){
        //Data validation
        $to=Data::email($to);
        $from=Data::email($from);
        $fromName=Data::htmlView($fromName);
        $subject=Data::htmlView($subject);
        $msg=Data::safe($msg);
        $cc=Data::email($cc);
        $bcc=Data::email($bcc);
        //Creating vars
        $this->to=$to;
        $this->subject=$subject;
        $this->msg=$msg;
        $this->headers = "From: \"".$fromName."\" <".$from.">\n";
        if($cc) $this->headers.= "CC: ".$cc."\n";
        if($bcc) $this->headers.= "BCC: ".$bcc."\n";
        if($file&&file_exists($file)){
            $f=fopen($file, "rb");
            $this->headers.= "Content-Type:multipart/mixed;";
            $un= strtoupper(uniqid(time()));
            $this->headers.= "boundary=\"----------".$un."\"\n\n";
            $this->msg = "------------".$un."\nContent-Type:text/html; charset=\"utf-8\"\n";
            $this->msg.= "Content-Transfer-Encoding: 8bit\n\n".$msg."\n\n";
            $this->msg.= "------------".$un."\n";
            $this->msg.= "Content-Type: application/octet-stream;";
            $this->msg.= "name=\"".basename($file)."\"\n";
            $this->msg.= "Content-Transfer-Encoding:base64\n";
            $this->msg.= "Content-Disposition:attachment;";
            $this->msg.= "filename=\"".basename($file)."\"\n\n";
            $this->msg.= chunk_split(base64_encode(fread($f,filesize($file))))."\n";
            fclose($f);
        }
        else{
            $this->headers.= "Content-type: text/html; charset=\"utf-8\"";
        }
        
    }
    //Send message
    public function send(){
        if(@mail($this->to, $this->subject, $this->msg, $this->headers)){
            return true;
        }
        else{
            return false;
        } 
    }
    //Get value
    public function __get($key){
        return $this->$key;
    }
    //Set value
    public function __set($key, $val){
        $this->$key=$val;
    }
}
?>
