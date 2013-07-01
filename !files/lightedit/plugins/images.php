<?
$path='../../../uploads/images/';
$dir=opendir($path);
print '[';
$first=true;
while($file=readdir($dir)){
    if($file=='.'||$file=='..'||is_dir($path.$file)){
        continue;
    }
    if(!$first) print ',';
    print '{"image":"uploads/images/'.$file.'","cover":"uploads/images/thumbs/'.$file.'"}';
    $first=false;
}
print ']';
?>