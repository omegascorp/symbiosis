<?
//Zip 0.0.3
class Zip{
    private $archive;
    public function __construct($archive){
        $this->archive=$archive;
    }
    public function unzip($folder){
        $zip=zip_open($this->archive);
        if(is_resource($zip)){
            while($zip_entry=zip_read($zip)){
                $name=zip_entry_name($zip_entry);
                $file=$folder.$name;
                if(substr($name, -1)=='/'){
                    if(!file_exists($file)) mkdir($file, 0755);
                }
                else{
                    if(zip_entry_open($zip, $zip_entry, "r")){
                        $buf=zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));
                        $f=fopen($file, "w");
                        fwrite($f, $buf);
                        zip_entry_close($zip_entry);
                    }
                }
            }
            zip_close($zip);
        }
        else
        {
	    return $this->zipFileErrMsg($zip);
        }
    }
    public function mod($folder, $mod){
        chmod($folder, $mode);
        $d=opendir($folder);
        while($dir=readdir($d)){
            if($dir!='.'&&$dir!='..'&&is_dir($dir)){
                $this->mod($folder.$dir.'/', $mod);
            }
        }
    }
    private function zipFileErrMsg($errno) {
	    // using constant name as a string to make this function PHP4 compatible
	    $zipFileFunctionsErrors = array(
		    'ZIPARCHIVE::ER_MULTIDISK' => 'Multi-disk zip archives not supported.',
		    'ZIPARCHIVE::ER_RENAME' => 'Renaming temporary file failed.',
		    'ZIPARCHIVE::ER_CLOSE' => 'Closing zip archive failed',
		    'ZIPARCHIVE::ER_SEEK' => 'Seek error',
		    'ZIPARCHIVE::ER_READ' => 'Read error',
		    'ZIPARCHIVE::ER_WRITE' => 'Write error',
		    'ZIPARCHIVE::ER_CRC' => 'CRC error',
		    'ZIPARCHIVE::ER_ZIPCLOSED' => 'Containing zip archive was closed',
		    'ZIPARCHIVE::ER_NOENT' => 'No such file.',
		    'ZIPARCHIVE::ER_EXISTS' => 'File already exists',
		    'ZIPARCHIVE::ER_OPEN' => 'Can\'t open file',
		    'ZIPARCHIVE::ER_TMPOPEN' => 'Failure to create temporary file.',
		    'ZIPARCHIVE::ER_ZLIB' => 'Zlib error',
		    'ZIPARCHIVE::ER_MEMORY' => 'Memory allocation failure',
		    'ZIPARCHIVE::ER_CHANGED' => 'Entry has been changed',
		    'ZIPARCHIVE::ER_COMPNOTSUPP' => 'Compression method not supported.',
		    'ZIPARCHIVE::ER_EOF' => 'Premature EOF',
		    'ZIPARCHIVE::ER_INVAL' => 'Invalid argument',
		    'ZIPARCHIVE::ER_NOZIP' => 'Not a zip archive',
		    'ZIPARCHIVE::ER_INTERNAL' => 'Internal error',
		    'ZIPARCHIVE::ER_INCONS' => 'Zip archive inconsistent',
		    'ZIPARCHIVE::ER_REMOVE' => 'Can\'t remove file',
		    'ZIPARCHIVE::ER_DELETED' => 'Entry has been deleted',
	    );
	    $errmsg = 'unknown';
	    foreach ($zipFileFunctionsErrors as $constName => $errorMessage) {
		    if (defined($constName) and constant($constName) === $errno) {
			    return 'Zip File Function error: '.$errorMessage;
		    }
	    }
	    return 'Zip File Function error: unknown';
    }
}
?>