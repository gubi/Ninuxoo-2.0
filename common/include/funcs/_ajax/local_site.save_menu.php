<?php
header("Content-type: text/plain");

foreach($output as $ok => $ov) {
	if($fs = fopen("../../../md/" . $ok . ".md", "w")) {
		fwrite($fs, $output[$ok] . PHP_EOL);
		fclose($fs);
		$data = "ok";
	} else {
		$data = "error::Non si gode dei permessi sufficienti per salvare il file.";
	}
}

print json_encode(array("data" => $data));
?>