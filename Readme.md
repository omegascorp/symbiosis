Requirements:
Apache, mode rewrite
PHP5, php-json
MySQL5

For the run automaticly instollation remove the content of config.php. (Size of config.php must be 0 bytes).

Manual installation:

1. Write in the config.php:
  <?
  $host='localhost';
  $user='[mysql_username]';
  $pass='[mysql_password]';
  $database='[database_name]';
  ?>

2. If your script location is http://localhost/symbiosis/ path is /symbiosis/.
Find in the file db/config.json row with title path and put here your path value.
Ex:
"path"=>"/symbiosis/"

3. If you use unix-like operating system change privileges of the folders !uploads, db, temp to 666.

4. MySql database locates in file db.sql. Import it into the [database_name].
