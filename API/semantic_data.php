<?php
if(count($_GET) == 0) {
	header("Content-type: text/plain");
	
	print "Nothing to do";
} else {
	if(isset($_GET["album"]) || isset($_GET["book"]) || isset($_GET["film"]) || isset($_GET["person"])) {
		require_once("../common/include/classes/get_semantic_data.class.php");
		
		$semantic_data = new semantic_data();
		if(isset($_GET["output"])) {
			$semantic_data->debug_output($_GET["output"]);
		}
		if(isset($_GET["format"])) {
			$semantic_data->format($_GET["format"]);
		}
		if($_GET["debug"] == "true") {
			$semantic_data->debug(true);
		}
		if(isset($_GET["album"])) {
			print $semantic_data->audio(rawurldecode($_GET["album"]));
		}
		if(isset($_GET["book"])) {
			print $semantic_data->book(rawurldecode($_GET["book"]));
		}
		if(isset($_GET["film"])) {
			print $semantic_data->film(rawurldecode($_GET["film"]));
		}
		if(isset($_GET["person"])) {
			print $semantic_data->person(rawurldecode($_GET["person"]));
		}
	} else {
		header("Content-type: text/plain");
		
		print "Nothing to do";
	}
}
?>