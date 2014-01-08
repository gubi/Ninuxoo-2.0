<?php
header("Content-type: text/plain");

$path = str_replace("common/", "../../../", $output["script_dir"]);
if(!file_exists($path)) {
	mkdir($path);
	chmod($path, 0777);
}
foreach(glob($path . "/*.md") as $filename) {
	$file[] = $filename;
}
if(count($file) > 0) {
	foreach($file as $filename) {
		$info = pathinfo($filename);
		
		print '<tr><td><input type="hidden" class="script_dir" value="' . $info["dirname"] . '"><input type="hidden" class="page_name" value="' . $info["filename"] . '"><a href="javascript:void(0);" title="Rimuovi questa pagina" class="text-danger remove_notice_btn remove_btn"><span class="glyphicon glyphicon-remove"></span></a></td><td><a href="./Dashboard/Pagine/' . rawurlencode(trim($info["filename"])) . '">' . base64_decode(rawurldecode(trim($info["filename"]))) . '</a></td><td style="color: #666;">' . date ("d/m/Y \a\l\l\e H:i:s", filemtime($filename)) . '</td><td></td></tr>';
	}
} else {
	print '<tr><td colspan="2" align="center"><span class="info">Nessuna pagina personale salvata</span></td></tr>';
}
?>