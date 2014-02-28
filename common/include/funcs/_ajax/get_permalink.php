<?php
header("Content-type: text/plain");
require_once("../../classes/class.url_shortener.php");

$yourls = new yourls();
print $yourls->shorten($output["url"]);
?>