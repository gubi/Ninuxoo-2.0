<?php
header("Content-type: text/plain");
require_once("common/include/classes/scan.class.php");

$scan = new scan();
if(isset($_GET["ajax"]) && trim($_GET["ajax"]) == "true") {
	$scan->save("ajax");
} else {
	$scan->save();
}
?>