# Malacia Surb Astvacacin church website (based on Symbiosis CMS)

Requirements:
Apache with mode rewrite, PHP5, php-json, MySQL5

## Automaticly instollation

Remove the content of config.php. (Size of config.php must be 0 bytes).
On unix-like systems privileges should be 666.

## Manual installation:

Write in the config.php:

    <?
    $host='localhost';
    $user='[mysql_username]';
    $pass='[mysql_password]';
    $database='[database_name]';
    ?>

If your script location is http://localhost/symbiosis/ path is /symbiosis/.
Find in the file db/config.json row with title path and put here your path value.
Ex:
"path"=>"/symbiosis/"

If you use unix-like operating system change privileges of the folders !uploads, db, temp to 666.

MySql database locates in file db.sql. Import it into the [database_name].
