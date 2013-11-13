<?php
/*
require("php_error.php");
if (function_exists('\php_error\reportErrors')) {
	\php_error\reportErrors();
}
*/
// Generate RSA key
if(!file_exists("common/include/conf/rsa_2048_priv.pem")) {
	shell_exec('openssl genrsa -out common/include/conf/rsa_2048_priv.pem 2048');
	if(!file_exists("common/include/conf/rsa_2048_pub.pem")) {
		shell_exec('openssl rsa -pubout -in common/include/conf/rsa_2048_priv.pem -out common/include/conf/rsa_2048_pub.pem');
	}
}
// Check if config exist else start setup
$has_config = (!file_exists("config.ini")) ? false : true;
if(!$has_config) {
	if(!isset($_GET["setup"])) {
		header("Location: ./?setup");
	} else {
		$config["NAS"]["name"] = "Local Semantic Ninuxoo setup";
		$NAS_absolute_uri = "http://" . $_SERVER["SERVER_ADDR"];
	}
} else {
	if(isset($_GET["setup"])) {
		header("Location: ./");
	}
	
	$config = parse_ini_file("config.ini", 1);
	$NAS_absolute_uri = preg_replace("{/$}", "", $config["NAS"]["http_root"]);
	$NAS_IP = $config["NAS"]["ipv4"];
}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php print $config["NAS"]["name"]; ?></title>
	
	<base href="./" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="author" content="Ninux.org Community - the Ninux Software Team" />
	<meta name="description" content="Ninux.org search engine" />
	
	<link rel="shortcut icon" href="common/media/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="common/css/main.css" type="text/css" media="screen" />
	<link rel="search" type="application/opensearchdescription+xml" title="Ninuxoo" href="osd.xml" />
	
	<script type="text/javascript" src="common/js/jquery-1.7.2.min.js"></script>
	<?php
	if($has_config) {
		require_once("common/tpl/has_config.tpl");
	} else {
		//
	}
	?>
</head>
<body>
	<?php
	require_once("common/tpl/menu.tpl");
	?>
	<div id="main_container">
		<div id="<?php print ($has_config ? "main_header" : "header"); ?>">
			<table>
				<tr>
					<td>
						<a href="">
							<img src="common/media/img/logo.png" alt="Logo Ninuxoo" />
						</a>
						<h1>
							<?php
							if($has_config) {
								print $config["NAS"]["name"];
							} else {
								print "Setup";
							}
							?>
						</h1>
					</td>
				</tr>
			</table>
		</div>
		<div id="container">
			<?php
			if($has_config) {
				require_once("common/tpl/content.tpl");
			} else {
				include("common/include/funcs/_ajax/check_internet_status.php");
				$btn_next_disabled = (check_internet_status() == "ok") ? "" : check_internet_status();
				require_once("common/tpl/setup.tpl");
			}
			?>
			<div id="footer">
				Powered by Ninux Community ~ the Ninux Software &amp; Communication Team :: icons made by <a href="http://www.picol.org/" target="_blank" title="PIctorial COmmunication Language - Richiede inoltro a Internet">Picol project</a>
			</div>
		</div>
	</div>
</body>
</html>
