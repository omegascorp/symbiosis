<?
if(!isset($_GET['uniq'])) exit();
session_start();
include('../../libraries/data/data.lib.php');
$unic=$_GET['uniq'];
$code=Data::uniq(5);
$_SESSION['capcha_'.$unic]=$code;
header("Content-type: image/png");
$im=imagecreate(150, 50);
$background_color = imagecolorallocate($im, 255, 255, 255);
//$text_color = imagecolorallocate($im, rand(0,200), rand(0,200), rand(0,200));
$text_color = imagecolorallocate($im, 60, 0, 0);
$black = imagecolorallocate($im, 0, 0, 0);

$fonts=array();

$dir=opendir('fonts');
while($file=readdir($dir)){
    if($file=='.'||$file=='..') continue;
    $fonts[]=$file;
}

$len=strlen($code);
for($i=0; $i<$len;$i++){
    imagettftext($im, rand(16, 26), rand(-30, 30), $i*25+5, 35, $text_color, 'fonts/'.$fonts[rand(0, count($fonts)-1)], $code[$i]);
}
imageline($im, rand(0,150), 0, rand(0,150), 50, $text_color);
imageline($im, rand(0,150), 0, rand(0,150), 50, $text_color);
imageline($im, rand(0,150), 0, rand(0,150), 50, $text_color);
imageline($im, rand(0,150), 0, rand(0,150), 50, $text_color);
imageline($im, rand(0,150), 0, rand(0,150), 50, $text_color);

imagepng($im);
imagedestroy($im);
?>