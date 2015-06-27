<?
if(!isset($_POST['username'])||!isset($_POST['password'])||!isset($_POST['email'])){
    print '{"error":"Not the all parametrs has been sent."}';
    exit();
}

$path=$_SERVER['REQUEST_URI'];
$pos=strrpos($path, '!install/');
$path=substr($path, 0, $pos);
$config=json_decode(file_get_contents('../db/config.json'));
$config->path=$path;
$file=fopen('../db/config.json', 'w');
fwrite($file, json_encode($config));
fclose($file);

$username=mysql_real_escape_string($_POST['username']);
$password=mysql_real_escape_string($_POST['password']);
$email=preg_replace("/[^A-z0-9_\-@]/", "", $_POST['email']);

/*
$config=json_encode(file_get_contents('../db/config.json'));
$config->title=$title;
$file=fopen('../db/config.json', 'w');
fwrite($file, json_decode($config));
fclose($file);
*/

include('../config.php');

@mysql_connect($host, $user, $pass) or die('{"error":"Can\'t connect to the db."}');
mysql_query('SET NAMES utf8');
@mysql_select_db($database) or die('{"error":"Can\'t select db."}');

mysql_query("UPDATE `users` SET username='".$username."', password=MD5('".$password."'), email='".$email."' WHERE id=1");
print '{"success":""}';
?>
