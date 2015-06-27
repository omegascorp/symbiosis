<?
$abbr=preg_replace("/[^A-z0-9]/", "", $_POST['abbr']);

$fileName='labels/'.$abbr.'.json';
if(!file_exists($fileName)){
    $abbr='en';
    $fileName='labels/'.$abbr.'.json';
}
$labels=json_decode(file_get_contents($fileName));
?>
<div id="middle">
    <div class="languages">
        <h2><?=$labels->step1?></h2>
        <ul>
            <li><a class="ui-corner-all <? if($abbr=='en'){ ?>selected<? } ?>" data-abbr="en">English</a></li>
            <li><a class="ui-corner-all <? if($abbr=='ru'){ ?>selected<? } ?>" data-abbr="ru">Русский</a></li>
        </ul>
    </div>
</div>
<div id="footer">
    <span class="back"><?=$labels->back?></span>
    <img src="!install/files/symbiosis.png" alt="Symbiosis Simple Magic" />
    <span class="next"><?=$labels->next?></span>
</div>