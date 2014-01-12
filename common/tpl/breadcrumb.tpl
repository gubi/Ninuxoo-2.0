<?php
function truncate($text, $length) {
	$length = abs((int)$length);
	if(strlen($text) > $length) {
		$text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
	}
	return($text);
}
function checkBase64Encoded($encodedString) {
	$length = strlen($encodedString);
	
	// Check every character.
	for ($i = 0; $i < $length; ++$i) {
		$c = $encodedString[$i];
		if (($c < '0' || $c > '9') && ($c < 'a' || $c > 'z') && ($c < 'A' || $c > 'Z') && ($c != '+') && ($c != '/') && ($c != '=')) {
			// Bad character found.
			return false;
		}
	}
	// Only good characters found.
	return true;
}
function optimize($string) {
	$string = str_replace("_", " ", $string);
	
	if(strpos($string, "/") !== false) {
		$info = pathinfo($string);
		$string = $info["basename"];
	}
	
	return truncate($string, 120);
}
if(isset($_GET["s"]) && trim($_GET["s"]) !== "" && !in_array(strtolower($page_name), $advanced_pages)) {
	?>
	<div id="breadcrumb"<?php print (strlen($GLOBALS["search_term"]) > 0 && $page_name_last !== "Ricerca avanzata") ? ' style="display:none;"' : ""; ?>>
		<ul>
			<li><a title="Pagina Principale" href="./" id="home"></a></li>
			<?php
			if(isset($_GET["q"]) && trim($_GET["q"]) !== "") {
				print '<li><a class="btn-link" href="./' . $_GET["s"] . '">' . optimize($_GET["s"]) . '</a></li>';
				
				if(isset($_GET["id"]) && trim($_GET["id"]) !== "") {
					
					print '<li><a class="btn-link" href="./' . $_GET["s"] . '/' . $_GET["q"] . '">' . optimize($_GET["q"]) . '</a></li><li>' . (base64_decode($_GET["id"] . "~", true) ? optimize(base64_decode($_GET["id"])) : optimize(($_GET["id"]))) . '</li>';
				} else {
					print '<li>' . optimize($_GET["q"]) . '</li>';
				}
			} else {
				if(strlen($GLOBALS["breadcrumb"]) == 0) {
					$GLOBALS["breadcrumb"] = optimize($_GET["s"]);
				}
				print '<li>' . $GLOBALS["breadcrumb"] . '</li>';
			}
			?>
		</ul>
	</div>
	<?php
}
?>