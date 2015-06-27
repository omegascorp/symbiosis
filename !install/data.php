<?
$abbr=preg_replace("/[^A-z0-9]/", "", $_POST['abbr']);

$fileName='labels/'.$abbr.'.json';
if(!file_exists($fileName)){
    $abbr='en';
    $fileName='labels/'.$abbr.'.json';
}
$labels=json_decode(file_get_contents($fileName));

?>
<div class="data">
    <h2><?=$labels->step4?></h2>
    <div class="username"><label><?=$labels->username?></label><input type="text" value="admin" /></div>
    <div class="password"><label><?=$labels->password?></label><input type="password" value="" /></div>
    <div class="email"><label><?=$labels->email?></label><input type="email" value="" /></div>
</div>