<?php
require_once("common/include/funcs/_blowfish.php");
require_once("common/include/lib/markdown.php");

if(isset($_GET["s"]) && trim($_GET["s"]) == "Esci") {
	setcookie ("n", "", time() - 3600);
	header("Location: ./");
}
if(isset($_COOKIE["n"])) {
	$c = explode("~", PMA_blowfish_decrypt($_COOKIE["n"], "ninuxoo_cookie"));
		$user["name"] = strstr($c[0], " ", true);
		$user["username"] = $c[1];
		$user["key"] = "0x" . $c[2];
	$username = $c[1];
	$general_settings = parse_ini_file("common/include/conf/general_settings.ini", 1);
	
	if(in_array(sha1($username), $general_settings["login"]["admin"])) {
		$GLOBALS["is_admin"] = true;
	} else {
		$GLOBALS["is_admin"] = false;
		if(isset($_GET["s"]) && trim(strtolower($_GET["s"])) == "admin") {
			header("Location: ./Dashboard");
		}
	}
}
// Generate RSA key
if(!file_exists("common/include/conf/rsa_2048_priv.pem")) {
	shell_exec('openssl genrsa -out common/include/conf/rsa_2048_priv.pem 2048');
	if(!file_exists("common/include/conf/rsa_2048_pub.pem")) {
		shell_exec('openssl rsa -pubout -in common/include/conf/rsa_2048_priv.pem -out common/include/conf/rsa_2048_pub.pem');
	}
}
if(isset($_GET["s"]) && trim($_GET["s"]) !== "") {
	$page_title = ucfirst(str_replace("_", " ", $_GET["s"]));
	$page_name = ucfirst(str_replace("_", " ", $_GET["s"]));
	
	if(isset($_GET["q"]) && trim($_GET["q"]) !== "") {
		$page_title .= " &rsaquo; " . ucfirst(str_replace("_", " ", $_GET["q"]));
		$page_name = ucfirst(str_replace("_", " ", $_GET["q"]));
		
		if(isset($_GET["id"]) && trim($_GET["id"]) !== "") {
			$page_title .= " &rsaquo; " . ucfirst(str_replace("_", " ", $_GET["id"]));
			$page_name = ucfirst(str_replace("_", " ", $_GET["id"]));
		}
	}
} else {
	$page_title = "";
}
$advanced_pages = array("admin", "dashboard", "sito locale");
// Check if config exist else start setup
$has_config = (!file_exists("common/include/conf/config.ini")) ? false : true;
if(!$has_config) {
	if(!isset($_GET["setup"])) {
		header("Location: http://" . preg_replace("/\/+/", "/", str_replace(array($_GET["s"], $_GET["id"], $_GET["q"]), "", $_SERVER[HTTP_HOST] . "/" . $_SERVER["REQUEST_URI"]) . "?setup"));
	} else {
		$config["NAS"]["name"] = "Local Semantic Ninuxoo setup";
		$NAS_absolute_uri = "http://" . $_SERVER["SERVER_ADDR"];
	}
} else {
	if(isset($_GET["setup"]) || isset($_GET["s"]) && trim($_GET["s"]) == "login" && isset($_COOKIE["n"])) {
		header("Location: ./");
	}
	
	$config = parse_ini_file("common/include/conf/config.ini", 1);
	$NAS_absolute_uri = preg_replace("{/$}", "", $config["NAS"]["http_root"]);
	$NAS_IP = $config["NAS"]["ipv4"];
}
?>
<!DOCTYPE html>
<html>
<head>
	<title><?php print $config["NAS"]["name"] . " | " . $page_title; ?></title>
	
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0">
	<base href="<?php print $config["NAS"]["http_root"]; ?>" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="author" content="Ninux.org Community - the Ninux Software Team" />
	<meta name="description" content="Ninux.org search engine" />
	
	<link rel="shortcut icon" href="common/media/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="common/css/bootstrap.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="common/js/font-awesome/css/font-awesome.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="common/css/main.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="common/css/device.css" type="text/css" media="screen" />
	<link rel="search" type="application/opensearchdescription+xml" title="Ninuxoo" href="osd.xml" />
	
	<script type="text/javascript" src="common/js/jquery-1.7.2.min.js"></script>
	<script src="common/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="common/js/Apprise/apprise-1.5.full.js"></script>
	<link rel="stylesheet" href="common/js/Apprise/apprise.css" type="text/css">
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
	<div id="page_loader"></div>
	<header>
		<?php
		require_once("common/tpl/menu.tpl");
		?>
	</header>
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
		<?php if($_GET["s"] !== "Admin" && $_GET["s"] !== "Dashboard" || isset($_GET["q"])) { require_once("common/tpl/breadcrumb.tpl"); } ?>
		<div id="container">
			<?php
			require_once("common/include/funcs/get_content.php");
			require_once("common/tpl/footer.tpl");
			?>
		</div>
	</div>
	<div id="superfooter">
		<a href="javascript:void(0);" onclick="$('html, body').animate({ scrollTop: ($('body').offset().top) }, 300);" class="btn btn-link"><span class="fa fa-angle-up"></span> <small>Top</small></a>
		<a href="javascript:void(0);" onclick="$('html, body').animate({ scrollTop: ($('body').offset().top) }, 300);" class="btn btn-link right"><small>Top</small> <span class="fa fa-angle-up"></span></a>
	</div>
</body>
</html>
