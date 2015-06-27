<?
$abbr=preg_replace("/[^A-z0-9]/", "", $_POST['abbr']);

$fileName='labels/'.$abbr.'.json';
if(!file_exists($fileName)){
    $abbr='en';
    $fileName='labels/'.$abbr.'.json';
}
$labels=json_decode(file_get_contents($fileName));

?>
<div class="db">
    <h2><?=$labels->step3?></h2>
    <div class="host"><label><?=$labels->host?></label><input class="ui-corner-all" type="text" value="localhost" /></div>
    <div class="username"><label><?=$labels->username?></label><input class="ui-corner-all" type="text" value="root" /></div>
    <div class="password"><label><?=$labels->password?></label><input class="ui-corner-all" type="password" value="" /></div>
    <div class="database"><label><?=$labels->database?></label><input class="ui-corner-all" type="text" value="symbiosis" /></div>
    <div class="message ui-corner-all"></div>
</div>