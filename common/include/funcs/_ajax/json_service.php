<?php
if(!function_exists("json_encode")) {
	if(class_exists("Services_JSON")) {
		require("JSON.php");
	} else {
		require("../../lib/JSON.php");
	}
	function json_encode($data) {
		$json = new Services_JSON();
		return $json->encode($data);
	}
}
if(!function_exists("json_decode")) {
	if(class_exists("Services_JSON")) {
		require("JSON.php");
	} else {
		require("../../lib/JSON.php");
	}
	function json_decode($data) {
		$json = new Services_JSON();
		return $json->decode($data);
	}
}
?>