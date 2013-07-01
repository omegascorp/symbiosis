<?
session_start();
header('Content-Type: text/html; charset=utf-8');
//$t1 = microtime(true);
$symbiont='';
if(isset($_POST['symbiont'])) $symbiont=$_POST['symbiont'];
else if(isset($_GET['symbiont'])) $symbiont=$_GET['symbiont'];

if($symbiont){
    include('libraries/kernel/kernel.lib.php');
    $kernel=new Kernel();
    $language=isset($_GET['language'])?$_GET['language']:'';
    $link=isset($_POST['link'])?$_POST['link']:'';
    $kernel->init(false, $link, $language);
    $kernel->addSymbiont('Script');
    $symbionts->Script->ajax();
    
    Design::symbiontEval($symbiont);
    print $kernel->vars->scripts;
    print $kernel->vars->styles;
    
    $kernel->destroy();
}
elseif(isset($_POST['file'])){
    $initKernel=isset($_POST['kernel'])&&$_POST['kernel']?true:false;
    
    if($initKernel){
        include('libraries/kernel/kernel.lib.php');
        $kernel=new Kernel();
        $language=isset($_GET['language'])?$_GET['language']:'';
        $link=isset($_GET['link'])?$_GET['link']:'';
        $kernel->init(false, $link, $language);
        
        $file=Data::fileSystem($_POST['file']);
        include('!symbionts/'.$file.'.php');
        
        $kernel->destroy();
    }
    else{
        include('libraries/data/data.lib.php');
        $file=Data::fileSystem($_POST['file']);
        include('!symbionts/'.$file.'.php');
    }
}

/*
$t2 = microtime(true);
echo "<br/>".($t2-$t1);
echo "<br/>".memory_get_usage()/1024;
*/
?>