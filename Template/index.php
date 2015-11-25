<?php
include_once('../helper/Debug.class.php');
include_once('Template.class.php');

$temp = new Template('templates/');
$bla = $temp->render('panel', [
    'title'=>"hallo"
], true);

Debug::e(htmlentities($bla));