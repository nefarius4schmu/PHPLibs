<?php
error_log(E_ALL);
define('ROOT', dirname(__FILE__).'/');
include_once(ROOT.'DB.class.php');
include_once(ROOT.'../helper/Debug.class.php');
include_once(ROOT.'../Generator/Table.class.php');
include_once(ROOT.'DBTable.class.php');

$table = new Table(['class'=>'table']);
$dbh = new DB(DB::DB_NefaDB);

$database = $dbh->database();
$tables = $dbh->tables();
$tables = $tables[key($tables)];
$tableInfo = [];
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Database Analyse</title>
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
        <h1>Database Analyse</h1>
        <section>
            <h2>Basic Info</h2>
            <h3>Database</h3>
            <?=Debug::r($database)?>
            <h3>Tables</h3>
            <?=$table->listing($tables)?>
            <h3>Columns</h3>
            <?php
            foreach($tables as $name){
                echo "<h4>${name}</h4>";
                $cols = $dbh->columns($name);
                echo $table->multi($cols);
                $tableInfo[$name] = new DBTable($name, $cols);
            }
            ?>
        </section>
        <section>
            <h3>Parsed</h3>
            <?=Debug::s($tableInfo)?>
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