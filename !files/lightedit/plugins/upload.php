<?
$eid=0;
$errors=array(
    '',
    'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    'The uploaded file was only partially uploaded',
    'No file was uploaded',
    '',
    'Missing a temporary folder',
    'Failed to write file to disk',
    'File upload stopped by extension',
    'No error code avaiable',
    'Can\'t save file',
    'File already exists',
    'Incorrect type'
);
$path='../../uploads/';
$name=$_FILES['file']['name'];
$eid=$_FILES['file']['error'];
$type=substr($name, strrpos($name, '.')+1);
if(!in_array($type, array('jpg','jpeg','png','gif'))){
    $eid=13;
}
if(!$eid){
    if(isset($_FILES['file']['tmp_name'])&&$_FILES['file']['tmp_name']!='none'){
        if(!@move_uploaded_file($_FILES['file']['tmp_name'], $path.$name)){
            $eid=9;
        }
    }
    else{
        $eid=4;
    }
}
if($eid==0){
    print '{"status":"success"}';
}
else{
    print '{"status":"error"}';
}
?>