<?php
header("Content-type:text/plain; charset=utf-8");
define('CHUNK_SIZE', 1024*1024); // Size (in bytes) of tiles chunk
require_once("common/include/classes/rsa.class.php");

function readfile_chunked($filename, $retbytes = true) { 
	$buffer = ''; 
	$cnt =0; 
	$handle = fopen($filename, 'rb'); 
	if ($handle === false) { 
		return false; 
	} 
	while (!feof($handle)) { 
		$buffer = fread($handle, CHUNK_SIZE); 
		echo $buffer; 
		ob_flush(); 
		flush(); 
		if ($retbytes) { 
			$cnt += strlen($buffer); 
		} 
	} 
	$status = fclose($handle); 
	if ($retbytes && $status) { 
		return $cnt; // return num. bytes delivered like readfile() does. 
	} 
	return $status; 
} 

$rsa = new rsa();
$url = parse_url($_SERVER["REQUEST_URI"]);
$config = parse_ini_file("common/include/conf/config.ini", 1);
if(strpos($url["query"], "view") === false) {
	$view = false;
	$hash = rawurldecode($url["query"]);
} else {
	$view = true;
	$hash = rawurldecode(str_replace(array("view=true&", "&view=true"), "", $url["query"]));
}

$locale = "it_IT.UTF-8";
setlocale(LC_ALL, $locale);
putenv("LC_ALL=" . $locale);
$filepath = trim(base64_decode($hash));
$tk = explode("://", $filepath);
$dest_token = $tk[0];
if($dest_token == $rsa->my_token()) {
	$file = str_replace("//", "/", $config["NAS"]["root_share_dir"] . "/" . $tk[1]);
} else {
	// Non è un file interno
	// Chiedo in mdns
}

$splinfo = new SplFileInfo($file);
if(file_exists($file) && $splinfo->isReadable()) {
	
	setlocale(LC_ALL, $locale);
	putenv("LC_ALL=" . $locale);
	
	if(!is_file($file)) {
		chdir($file);
		setlocale(LC_ALL, $locale);
		putenv("LC_ALL=" . $locale);
		$content = array_map("trim", explode(",", shell_exec("ls -m")));
		foreach($content as $k => $v) {
			$content[$k] = str_replace(" ", "\ ", escapeshellcmd($file . "/" . $v));
		}
		$contents = implode(" ", $content);
		
		header("Content-Type: application/zip");
		header("Content-Disposition: attachment; filename=\"" . basename($splinfo->getFilename()) . ".zip\"");
		header("Content-Transfer-Encoding: binary");
		
		// Zip creation on the fly
		setlocale(LC_ALL, $locale);
		putenv("LC_ALL=" . $locale);
		$fp = popen("zip -0 -j -q -r - " . $contents, "r");
		while(!feof($fp)) {
			print fread($fp, 8192);
		}
		pclose($fp);
		exit();
	} else {
		require_once("common/include/lib/mime_types.php");
		if($view) {
			switch($mime_type[$splinfo->getExtension()]["type"]) {
				case "text":
				case "image":
				case "ebook":
					header("Content-type: " . $mime_type[$splinfo->getExtension()]["mime"]);
					header("Content-disposition: inline; filename=\"" . basename($splinfo->getFilename()) . "\"");
					header("Content-Transfer-Encoding: binary");
					header("Content-Length: " . $splinfo->getSize());
					header("Accept-Ranges: bytes");
					break;
				// Add other cases of extension group
				// that you want to view on browser
			}
			$text = file_get_contents($file);
			if($mime_type[$splinfo->getExtension()]["type"] == "text" || $mime_type[$splinfo->getExtension()]["type"] == "ebook") {
				print utf8_encode($text);
			} else {
				print $text;
			}
		} else {
			header("Pragma: public"); // required
			header("Expires: 0"); // no cache
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s", $splinfo->getMTime()) . " GMT");
			header("Cache-Control: private", false);
			header("Content-Type: " . $mime_type[$splinfo->getExtension()]["mime"]);
			header("Content-disposition: attachment; filename=\"" . basename($splinfo->getFilename()) . "\"");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . $splinfo->getSize()); // provide file size
			header("Connection: close");
			
			@readfile_chunked($file);
		}
		exit();
	}
} else {
	print $file . " is not a valid directory or file";
}
?>