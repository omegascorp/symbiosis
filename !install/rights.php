<?
$abbr=preg_replace("/[^A-z0-9]/", "", $_POST['abbr']);

$fileName='labels/'.$abbr.'.json';
if(!file_exists($fileName)){
    $abbr='en';
    $fileName='labels/'.$abbr.'.json';
}
$labels=json_decode(file_get_contents($fileName));

$db=@fopen('../db/1.txt', 'w');
if($db){
    fclose($db);
    @unlink('../db/1.txt');
}
$config=@fopen('../config.php', 'w');
if($config){
    fclose($config);
}
$uploads=@fopen('../!uploads/1.txt', 'w');
if($uploads){
    fclose($uploads);
    @unlink('../!uploads/1.txt');
}
$temp=@fopen('../temp/1.txt', 'w');
if($temp){
    fclose($temp);
    @unlink('../temp/1.txt');
}
?>
<div class="rights">
    <h2><?=$labels->step2?></h2>
    <ul>
        <li>
            <span class="ui-widget">
                <span class="ui-icon ui-icon-<? if($db){ ?>check<? }else{ ?>closethick<? } ?>"></span>
            </span>
            <label>db</label>
        </li>
        <li>
            <span class="ui-widget">
                <span class="ui-icon ui-icon-<? if($uploads){ ?>check<? }else{ ?>closethick<? } ?>"></span>
            </span>
            <label>!uploads</label>
        </li>
        <li>
            <span class="ui-widget">
                <span class="ui-icon ui-icon-<? if($temp){ ?>check<? }else{ ?>closethick<? } ?>"></span>
            </span>
            <label>temp</label>
        </li>
        <li>
            <span class="ui-widget">
                <span class="ui-icon ui-icon-<? if($config){ ?>check<? }else{ ?>closethick<? } ?>"></span>
            </span>
            <label>config.php</label>
        </li>
    </ul>
    <div class="buttons">
        <span class="refresh"><?=$labels->refresh?></span>
    </div>
</div>