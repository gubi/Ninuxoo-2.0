<?php
if(count($_GET) == 0) {
	header("Content-type: text/plain");
	
	print "Nothing to do";
} else {
	if(isset($_GET["album"]) || isset($_GET["book"]) || isset($_GET["film"]) || isset($_GET["person"]) || isset($_GET["thing"])) {
		require_once("../../common/include/classes/get_semantic_data.class.php");
		
		$semantic_data = new semantic_data();
		if(isset($_GET["output"])) {
			$semantic_data->output($_GET["output"]);
		}
		if(isset($_GET["format"])) {
			$semantic_data->format($_GET["format"]);
		} else {
			$semantic_data->format();
		}
		if($_GET["debug"] == "true") {
			$semantic_data->debug(true);
		}
		if(isset($_GET["album"])) {
			$semantic_data->audio(rawurldecode($_GET["album"]));
		}
		if(isset($_GET["book"])) {
			$semantic_data->book(rawurldecode($_GET["book"]));
		}
		if(isset($_GET["film"])) {
			$semantic_data->film(rawurldecode($_GET["film"]));
		}
		if(isset($_GET["person"])) {
			$semantic_data->person(rawurldecode($_GET["person"]));
		}
		if(isset($_GET["thing"])) {
			$semantic_data->thing(rawurldecode($_GET["thing"]));
		}
		$semantic_data->export();
	} else {
		header("Content-type: text/plain");
		
		print "Nothing to do";
	}
}
?>