<?
if(!isset($_GET['unic'])) exit();
session_start();
$code=$_SESSION['capcha_'.$_GET['unic']];
$drow=new ImagickDraw();
$drow->setFontSize(25);
$im = new Imagick();
$im->newImage(100, 50, "white");
$im->annotateImage($drow,10, 30, 0, $code);
$r1=rand(10, 50);
$r2=rand(10, 50);
$im->rollImage($r1, 0);
$im->swirlImage((-1)^rand(1, 2)*rand(10, 50));
$im->rollImage($r2, 0);
$im->swirlImage((-1)^rand(1, 2)*rand(10, 50));
$im->rollImage(200-$r1-$r2, 0);
$im->setImageFormat('png');
header("Content-Type: image/png");
echo $im;
?>