<?php
require_once("common/include/funcs/_blowfish.php");
require_once("common/include/lib/markdown.php");

if(isset($_GET["s"]) && trim($_GET["s"]) == "Esci") {
	setcookie ("n", "", time() - 3600);
	header("Location: ./");
}
if(isset($_GET["c"])) {
	header("Location: ./Cerca:" . ucfirst($_GET["c"]));
}
if(strpos($_GET["s"], "Scarica:") !== false) {
	require_once("common/include/funcs/download.php");
}
function mb_pathinfo($filepath) {
	preg_match('%^(.*?)[\\\\/]*(([^/\\\\]*?)(\.([^\.\\\\/]+?)|))[\\\\/\.]*$%im',$filepath,$m);
	if($m[1]) $ret['dirname']=$m[1];
	if($m[2]) $ret['basename']=$m[2];
	if($m[5]) $ret['extension']=$m[5];
	if($m[3]) $ret['filename']=$m[3];
	return $ret;
}
$search_types = array("query" => "Tutti i risultati possibili", "exactquery" => "Testo esatto", "orquery" =>"Singola parola", "likequery" => "Somiglianza nei termini", "whatsnew" => "Nuova scansione del crawler");
// Generate RSA key
if(!file_exists("common/include/conf/rsa_2048_priv.pem")) {
	shell_exec('openssl genrsa -out common/include/conf/rsa_2048_priv.pem 2048');
	if(!file_exists("common/include/conf/rsa_2048_pub.pem")) {
		shell_exec('openssl rsa -pubout -in common/include/conf/rsa_2048_priv.pem -out common/include/conf/rsa_2048_pub.pem');
	}
}
$GLOBALS["general_settings"] = parse_ini_file("common/include/conf/general_settings.ini", true);
$GLOBALS["config"] = parse_ini_file("common/include/conf/config.ini", true);
// Regenerate caching dir
if($GLOBALS["general_settings"]["caching"]["allow_caching"] == "true") {
	if(!file_exists($GLOBALS["config"]["NAS"]["root_share_dir"] . ".ninuxoo_cache")) {
		mkdir($GLOBALS["config"]["NAS"]["root_share_dir"] . ".ninuxoo_cache/");
		chmod($GLOBALS["config"]["NAS"]["root_share_dir"] . ".ninuxoo_cache/", 0777);
	}
}
if(isset($_GET["s"]) && trim($_GET["s"]) !== "") {
	$page_title = ucfirst(str_replace("_", " ", $_GET["s"]));
	$page_name = ucfirst(str_replace("_", " ", $_GET["s"]));
	$page_name_last = "";
	$search_term = ((strpos($_GET["s"], "Cerca:") !== false) ? str_replace("Cerca:", "", $_GET["s"]) : "");
	
	if(isset($_GET["q"]) && trim($_GET["q"]) !== "") {
		$page_title .= " &rsaquo; " . ucfirst(str_replace("_", " ", $_GET["q"]));
		$page_name = ucfirst(str_replace("_", " ", $_GET["q"]));
		$page_name_last = ucfirst(str_replace("_", " ", $_GET["s"]));
		$search_term = ((strpos($_GET["q"], "Cerca:") !== false) ? str_replace("Cerca:", "", $_GET["q"]) : "");
		
		if(isset($_GET["id"]) && trim($_GET["id"]) !== "") {
			$page_title .= " &rsaquo; " . ucfirst(str_replace("_", " ", $_GET["id"]));
			$page_name = ucfirst(str_replace("_", " ", $_GET["id"]));
			$page_name_last = ucfirst(str_replace("_", " ", $_GET["q"]));
			$search_term = ((strpos($_GET["id"], "Cerca:") !== false) ? str_replace("Cerca:", "", $_GET["id"]) : "");
		}
	}
	if(strlen($search_term) > 0 && $page_name_last !== "Ricerca avanzata") {
		$page_title = "Risultati della ricerca per \"" . $search_term . "\"";
		$GLOBALS["breadcrumb"] = "Ricerca di \"" . $search_term . "\"";
	}
	if(strpos($_GET["s"], "Scheda:") !== false || strpos($_GET["s"], "Esplora:") !== false) {
		require_once("common/include/classes/rsa.class.php");
		
		$rsa = new rsa();
		$hash = rawurldecode(str_replace(array("/Scheda:?", "/Esplora:?"), "", $_SERVER["REQUEST_URI"]));
		$filepath = trim(base64_decode($hash));
		$tk = explode("://", $filepath);
		$GLOBALS["dest_token"] = $tk[0];
		if($GLOBALS["dest_token"] == $rsa->my_token()) {
			$file = str_replace("///", "/", $GLOBALS["config"]["NAS"]["root_share_dir"] . "/" . $tk[1]);
			$GLOBALS["root_dir"] = $GLOBALS["config"]["NAS"]["root_share_dir"];
			$info = mb_pathinfo($file);
		} else {
			$GLOBALS["root_dir"] = "";
			// Non è un file interno
			// Chiedo in mdns
		}
		$filename = $info["basename"];
		
		if(strpos($_GET["s"], "Scheda:") !== false) {
			$search_term = $filename;
			$GLOBALS["breadcrumb"] = "Dettagli sul file \"" . $search_term . "\"";
			$page_title = "Dettagli del file per \"" . $search_term . "\"";
		} else {
			$search_term = $filename;
			$GLOBALS["breadcrumb"] = "Directory \"" . $search_term . "\"";
			$page_title = "Esplorazione della directory \"" . $search_term . "\"";
		}
	}
} else {
	$page_title = "";
	$search_term = "";
}

$advanced_pages = array("accedi", "admin", "dashboard", "sito_locale");
// Check if config exist else start setup
$has_config = (!file_exists("common/include/conf/config.ini")) ? false : true;
if(!$has_config) {
	if(!isset($_GET["setup"])) {
		header("Location: http://" . preg_replace("/\/+/", "/", str_replace(array($_GET["s"], $_GET["id"], $_GET["q"]), "", $_SERVER["HTTP_HOST"] . "/" . $_SERVER["REQUEST_URI"]) . "?setup"));
	} else {
		$GLOBALS["config"]["NAS"]["name"] = "Local Semantic Ninuxoo setup";
		$NAS_absolute_uri = "http://" . $_SERVER["SERVER_ADDR"];
	}
} else {
	if(isset($_GET["setup"]) || isset($_GET["s"]) && trim($_GET["s"]) == "login" && isset($_COOKIE["n"])) {
		header("Location: ./");
	}
	$NAS_absolute_uri = preg_replace("{/$}", "", $GLOBALS["config"]["NAS"]["http_root"]);
	$NAS_IP = $GLOBALS["config"]["NAS"]["ipv4"];
	
	$GLOBALS["search_term"] = $search_term;
	$GLOBALS["search_type"] = $GLOBALS["general_settings"]["searches"]["research_type"];
	$GLOBALS["search_num_results"] = $GLOBALS["general_settings"]["searches"]["research_results"];
	$GLOBALS["search_path"] = "";
	$GLOBALS["search_filetype"] = "";
}
if(isset($_COOKIE["n"])) {
	$c = explode("~", PMA_blowfish_decrypt($_COOKIE["n"], "ninuxoo_cookie"));
		$user["name"] = strstr($c[0], " ", true);
		$user["email"] = $c[1];
		$user["key"] = "0x" . $c[2];
	$username = $c[1];
	$GLOBALS["user_settings"] = parse_ini_file("common/include/conf/user/" . sha1($username) . "/user.conf", true);
	
	if(in_array(sha1($username), $GLOBALS["general_settings"]["login"]["admin"])) {
		$GLOBALS["is_admin"] = true;
	} else {
		$GLOBALS["is_admin"] = false;
		if(isset($_GET["s"]) && trim(strtolower($_GET["s"])) == "admin") {
			header("Location: ./Dashboard");
		}
	}
}
$logo_img = '<img src="common/media/img/logo.png" alt="Logo Ninuxoo" /><h1>' . (($has_config) ? $GLOBALS["config"]["NAS"]["name"] : "Setup") .'</h1>';
?>
<!DOCTYPE html>
<html>
<head>
	<title>Ninuxoo <?php print $GLOBALS["config"]["NAS"]["name"] . " | " . $page_title; ?></title>
	
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<base href="<?php print $GLOBALS["config"]["NAS"]["http_root"]; ?>" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="author" content="Ninux.org Community - the Ninux Software Team" />
	<meta name="description" content="Ninux.org search engine" />
	
	<link rel="shortcut icon" href="common/media/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="common/css/bootstrap.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="common/js/font-awesome/css/font-awesome.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="common/css/picol-font-awesome.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="common/css/main.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="common/css/device.css" type="text/css" media="screen" />
	<link rel="search" type="application/opensearchdescription+xml" title="Ninuxoo" href="osd.xml" />
	
	<script type="text/javascript" src="common/js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="common/js/jquery-ui-1.10.3.custom/js/jquery-ui-1.10.3.custom.min.js"></script>
	<script src="common/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="common/js/apprise-bootstrap.js"></script>
	<?php
	if($has_config && trim(strtolower($_GET["s"])) !== "admin") {
		require_once("common/tpl/has_config.tpl");
	}
	?>
	<?php
	if(isset($_COOKIE["n"])) {
		?>
		<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
		<script type="text/javascript" src="common/js/include/common.js"></script>
		<script type="text/javascript" src="common/js/jquery.easing.1.3.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			check_notify();
		});
		</script>
		<?php
	}
	?>
	<script type="text/javascript">
	(function() {
		window.alert = function(string, args, callback) {
			return apprise(string, args, callback);
		};
	})();
	$(document).ready(function() {
		$(".merged input, .merged-xs input").on({ focus: function() { $(this).parent().addClass("focusedInput") }, blur: function() { $(this).parent().removeClass("focusedInput") }});
		$("a[title]:not(#footer > a)").tooltip({placement: "auto"});
		$("button[title], abbr[title], acronym[title]").tooltip({placement: "auto"});
		$("*[data-content]:not(#footer > a)").popover({placement: "auto"});
		$("#footer a[title]").popover({placement: "auto", trigger: "hover"});
		$(window).scroll(function(){
			if ($(this).scrollTop() > 100) {
				$("#superfooter").fadeIn();
			} else {
				$("#superfooter").fadeOut();
			}
		});
	});
	</script>
</head>
<body>
	<span style="display: none;" id="notification_refresh"><?php print $GLOBALS["user_settings"]["Chat"]["refresh_interval"]; ?></span>
	<div id="page_loader"></div>
	<header>
		<?php
		require_once("common/tpl/menu.tpl");
		?>
	</header>
	<!--div id="main_container"-->
		<div id="<?php print ($has_config ? "main_header" : "header"); ?>">
			<div>
				<div id="logo" class="center-block">
					<?php print (trim($page_name) !== "") ? '<a href="" title="Torna alla Pagina Principale">' . $logo_img . '</a>' : $logo_img; ?>
				</div>
			</div>
		</div>
		<?php if($_GET["s"] !== "Admin" && $_GET["s"] !== "Dashboard" || isset($_GET["q"])) { require_once("common/tpl/breadcrumb.tpl"); } ?>
		<div id="container">
			<?php
			require_once("common/include/funcs/get_content.php");
			?>
		</div>
		<?php require_once("common/tpl/footer.tpl"); ?>
	<!--/div-->
	<?php require_once("common/tpl/superfooter.tpl"); ?>
</body>
</html>
