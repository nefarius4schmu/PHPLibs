<?php
error_log(E_ALL);
define('ROOT', dirname(__FILE__).'/');
include_once(ROOT.'Connect.class.php');
include_once(ROOT.'../helper/Debug.class.php');

$dbh = new Connect(Connect::DB_NefaDB);
Debug::v($dbh->map('SELECT * FROM tags;', function(&$rows, $row){$rows[] = $row;}));


Debug::s('time elapsed: '.round(Debug::getEndTime(), 2).'s');