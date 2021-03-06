<?php
error_log(E_ALL);
define('ROOT', dirname(__FILE__).'/');
include_once(ROOT.'DB.class.php');
include_once(ROOT.'../helper/Debug.class.php');
include_once(ROOT.'../Generator/Table.class.php');

$table = new Table(['class'=>'table']);
$dbh = new DB(DB::DB_NefaDB);
$query = 'SELECT * FROM tags;';
$map = $dbh->map($query, function(&$rows, $row){$rows[] = $row;});
$value = $dbh->value($query);
$item = $dbh->item($query);
$listing = $dbh->listing($query);
$database = $dbh->database();
$tables = $dbh->tables();

?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Database Class Tests</title>
    <meta charset="UTF-8"/>
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <style>
        .wrapper{
            width: 768px;
            margin: 50px auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <h1>Database Class Tests</h1>
        <section>
            <h2>Value</h2>
            <?=Debug::r($value)?>
        </section>
        <section>
            <h2>Item</h2>
            <?=$table->listing($item)?>
        </section>
        <section>
            <h2>Listing</h2>
            <?=$table->listing($listing)?>
        </section>
        <section>
            <h2>Map</h2>
            <?=$table->basic($map, ['class'=>'table'])?>
        </section>
        <section>
            <h2>DB</h2>
            <?=Debug::r($database)?>
            <?=$database !== false && $tables !== false ? $table->listing($tables[key($tables)], ['class'=>'table']) : Debug::e('failed to get database infos')?>
        </section>
        <section>
            <h2>Debug</h2>
            <?php
//                Debug::v($map);
                Debug::s('time elapsed: '.round(Debug::getEndTime(), 2).'s')
            ?>
        </section>
    </div>
</body>
</html>