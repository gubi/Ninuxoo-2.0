<?php
header("Content-type: text/plain");
require_once("../../classes/url_shortener.class.php");

$yourls = new yourls();
print $yourls->shorten($output["url"]);
?>