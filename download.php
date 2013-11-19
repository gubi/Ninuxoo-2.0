<?php
header("Content-type:text/plain; charset=utf-8");
require_once("common/include/classes/rsa.class.php");

$rsa = new rsa();
$data = trim($rsa->simple_private_decrypt(rawurldecode($_GET["h"])));

$info = pathinfo(urldecode($data));
$filename = $info["basename"];

$config = parse_ini_file("common/include/conf/config.ini", 1);
$file = $config["NAS"]["smb_conf_dir"] . ltrim($data, "/");

if(file_exists($file)) {
	if(!is_file($file)) {
		print $file . "\n\n";
		chdir($file);
		$content = array_map("trim", explode(",", shell_exec("ls -m")));
		foreach($content as $k => $v) {
			$content[$k] = str_replace(" ", "\ ", escapeshellcmd($file . "/" . $v));
		}
		$contents = implode(" ", $content);
		
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="' . basename($filename) . '.zip"');
		header('Content-Transfer-Encoding: binary');
		
		// Zip creation on the fly
		$fp = popen("zip -0 -j -q -r - " . $contents, "r");
		while(!feof($fp)) {
			print fread($fp, 8192);
		}
		pclose($fp);
		exit();
	} else {
		$mime_type = array(
			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',
			
			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',
			
			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',
			
			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',
			'avi' => 'video/x-msvideo',
			
			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',
			
			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',
			
			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);
		
		header("Pragma: public"); // required
		header("Expires: 0"); // no cache
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s", filemtime($file)) . " GMT");
		header("Cache-Control: private", false);
		header("Content-Type: " . $mime_type[$info["extension"]]);
		header("Content-disposition: attachment; filename=\"" . basename($filename) . "\"");
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: " . filesize($file)); // provide file size
		header("Connection: close");

		readfile($file);
		exit();
	}
} else {
	print $file . " is not a valid directory or file";
}
?>