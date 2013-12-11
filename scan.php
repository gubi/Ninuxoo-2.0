<?php
header("Content-type: text/plain");
require_once("common/include/classes/scan.class.php");

$scan = new scan();
$scan->save();
?>