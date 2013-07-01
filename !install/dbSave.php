<?
if(!isset($_POST['host'])||!isset($_POST['username'])||!isset($_POST['password'])||!isset($_POST['database'])){
    print '{"error":"Not the all parametrs has been sent."}';
    exit();
}
$host=preg_replace("/[^A-z0-9_\-]/", "", $_POST['host']);
$username=preg_replace("/[^A-z0-9_\-]/", "", $_POST['username']);
$password=preg_replace("/[^A-z0-9_\-@?!~#$%&*]/", "", $_POST['password']);
$database=preg_replace("/[^A-z0-9_\-]/", "", $_POST['database']);

@mysql_connect($host, $username, $password) or die('{"error":"Can\'t connect to the db."}');
mysql_query('SET NAMES utf8');
@mysql_select_db($database) or die('{"error":"Can\'t select db."}');

$config=fopen("../config.php", "w");
$file=
"<?\r\n".
"\$host='".$host."';\r\n".
"\$user='".$username."';\r\n".
"\$pass='".$password."';\r\n".
"\$database='".$database."';\r\n".
"?>";
fwrite($config, $file);
fclose($config);

$sql=file_get_contents('../db.sql');
$sql=preg_replace("/(\-\-(.*))((\r\n)|(\n)|(\r))/", "", $sql);
$sql=preg_replace("/\/\*(.*)\*\//Us", "", $sql);

while($x=strpos($sql, ";\r\n")|strpos($sql, ";\n")|strpos($sql, ";\r")){
    $query=substr($sql, 0, $x);
    $sql=substr($sql, $x+1);
    mysql_query($query);
}

print '{"success":""}';

?>