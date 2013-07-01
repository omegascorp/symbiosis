<?
/*
Symbiosis Dark Side
https://github.com/omegascorp/symbiosis/
*/

session_start();
header('Content-Type: text/html; charset=utf-8');
//$t1 = microtime(true);
if(!file_exists("config.php")||filesize("config.php")==0){
    include('!install/index.php');
    exit();
}

include('libraries/kernel/kernel.lib.php');
$kernel=new Kernel();
$kernel->init();

$kernel->addSymbiont("Main");
$symbionts->Main->main();

$kernel->destroy();

/*
$t2 = microtime(true);
echo "<br/>".($t2-$t1);
echo "<br/>".memory_get_usage()/1024;
*/
?>
