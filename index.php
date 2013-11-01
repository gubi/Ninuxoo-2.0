<?php
// Check if config exist else start setup
$has_config = (!file_exists("config.ini")) ? false : true;
if(!$has_config) {
	$config["NAS"]["name"] = "Local Semantic Ninuxoo setup";
	$NAS_absolute_uri = "http://" . $_SERVER["SERVER_ADDR"];
	/*
	ob_start();
	require("scan.php");
	$data = ob_get_clean();
	ob_end_clean();
	header("Content-type: text/html");
	*/
} else {
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
	<link rel="search" type="application/opensearchdescription+xml" title="Ninuxoo NAS LittleGym" href="osd.xml" />
	
	<script type="text/javascript" src="common/js/jquery-1.7.2.min.js"></script>
	<?php
	if($has_config) {
		require_once("common/tpl/has_config.tpl");
	} else {
	
	}
	?>
</head>
<body>
	<div id="top_menu">
		<?php
		if($has_config) {
			require_once("common/tpl/menu.tpl");
		} else {
		
		}
		?>
	</div>
	<div id="main_container">
		<div id="<?php print ($has_config ? "main_header" : "header"); ?>">
			<table>
				<tr>
					<td>
						<a href="">
							<img src="<?php print $NAS_absolute_uri; ?>/common/media/img/logo.png" alt="Logo Ninuxoo" />
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
