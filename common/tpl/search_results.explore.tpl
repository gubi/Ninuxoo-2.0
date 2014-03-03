<?php
require_once("common/include/lib/mime_types.php");
function taglia_stringa($stringa, $max_char, $ellipses = "..."){
	if (strlen($stringa) > $max_char){
		$str_info = pathinfo($stringa);
		$stringa_tagliata = substr($str_info["basename"], 0, $max_char);
		$last_space = strrpos($stringa_tagliata, " ");
		$stringa_ok = substr($stringa_tagliata, 0, $last_space);
		
		return $stringa_ok . "" . $ellipses . " ." . $str_info["extension"];
	} else {
		return $stringa;
	}
}
function convert_file_size($bytes) {
	$bytes = floatval($bytes);
	$arBytes = array(
		0 => array("unit" => "Tb", "value" => pow(1024, 4)),
		1 => array("unit" => "Gb", "value" => pow(1024, 3)),
		2 => array("unit" => "Mb", "value" => pow(1024, 2)),
		3 => array("unit" => "Kb", "value" => 1024),
		4 => array("unit" => "b", "value" => 1),
	);

	foreach($arBytes as $arItem) {
		if($bytes >= $arItem["value"]) {
			$result = $bytes / $arItem["value"];
			$result = str_replace(".", ",", strval(round($result, 2))) . "<small>" . $arItem["unit"] . "</small>";
			break;
		}
	}
	return $result;
}
function special_escapeshellcmd($string) {
	return str_replace(array("//", " ", "'", "\""), array("/", "\ ", "\'", "\\\""), escapeshellcmd($string));
}

?>
<link rel="stylesheet" href="common/js/jquery.treeview/jquery.treeview.css" type="text/css" media="screen" />
<script type="text/javascript" src="common/js/jquery.treeview/jquery.treeview.js"></script>
<script type="text/javascript" src="common/js/jquery.highlight-4.js"></script>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript" src="common/js/include/local_search.explore.js"></script>

<span style="display: none;" id="result_type">Explore</span>
<span style="display: none;" id="dir_hash"><?php print rawurlencode($hash); ?></span>
<div class="well clearfix" style="margin-top: 5em;">
	<ol class="breadcrumb col-sm-11">
		<?php
		$path = explode("/", str_replace($GLOBALS["config"]["NAS"]["root_share_dir"], "", $file));
		$a_path = "";
		$b_path = $GLOBALS["config"]["NAS"]["root_share_dir"];
		if($file !== $b_path) {
			$c_path[] = '<li><a href="./Esplora:?' . rawurlencode(base64_encode($GLOBALS["dest_token"] . "://")) . '" title="Vedi al contenuto di questa directory"><span class="fa fa-folder-o"></span></a></li>';
			foreach($path as $directory) {
				if($directory !== $filename && strlen($directory) > 0) {
					$a_path .= "/" . $directory;
					$c_path[] = '<li><a href="./Esplora:?' . rawurlencode(base64_encode(str_replace("//", "/", $GLOBALS["dest_token"] . "://" . $a_path))) . '" title="Vedi al contenuto di questa directory">' . $directory . '</a></li>';
				}
			}
			$c_path[] = '<li class="active">' . $filename . '</li>';
		} else {
			$c_path[] = '<li class="active"><span class="fa fa-folder-o"></span> Directory principale</li>';
		}
		$complete_path = implode("", $c_path);
		?>
		<?php print $complete_path; ?>
	</ol>
	<div class="btn-group col-sm-1">
		<a href="javascript:void(0);" id="grid" class="btn btn-default active"><span class="fa fa-th-large"></span></a>
		<a href="javascript:void(0);" id="list" class="btn btn-default"><span class="fa fa-th-list"></span></a>
	</div>
</div>
<div id="explore_content" class="row grid-group">
	<?php
	if(!is_dir($file . "/" . $dir)) {
		?>
		<div class="alert alert-danger" id="unreadable"><span class="fa fa-times"></span>&nbsp;&nbsp;Attenzione: La risorsa non risulta leggibile!</div>
		<?php
	} else {
		$locale = "it_IT.UTF-8";
		setlocale(LC_ALL, $locale);
		putenv("LC_ALL=" . $locale);
		$ls = explode("\n", trim(shell_exec("ls -B -N " . str_replace(" ", "\ ", escapeshellcmd($file)))));
		foreach($ls as $dir) {
			$thumb = "";
			if(is_dir($file . "/" . $dir)) {
				$thumbs = glob($file . "/" . $dir . "/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
				$thumb = '<img src="./Scarica:?view=true&' . rawurlencode(base64_encode($GLOBALS["dest_token"] . "://" . str_replace($GLOBALS["config"]["NAS"]["root_share_dir"], "", $thumbs[rand(0, (count($thumbs) -1))]))) . '" alt="folder" style="box-shadow: 2px 2px 4px #666; border: #ccc 1px solid; width: 128px; height: auto;" />';
				$preview = '<i class="fa fa-folder-o btn" style="font-size: 128px;"></i>';
			} else {
				$i = pathinfo($file . "/" . $dir);
				if($i["extension"] == "pdf" && !file_exists($file . "/" . str_replace("pdf", "png", $dir))) {
					// Convert first page of pdf in png preview
					shell_exec("convert " . str_replace(" ", "\ ", escapeshellcmd($file . "/" . $dir)) . "[0] -thumbnail x128 " . str_replace(" ", "\ ", escapeshellcmd($file . "/" . str_replace("pdf", "png", $dir))));
				}
				if($i["extension"] !== "pdf") {
					if($mime_type[$i["extension"]]["type"] == "image") {
						$preview = '<img src="./Scarica:?view=true&' . rawurlencode(base64_encode($GLOBALS["dest_token"] . "://" . implode("/", $path) . "/" . $dir)) . '" alt="folder" style="box-shadow: 2px 2px 4px #666; border: #ccc 1px solid; width: 128px; height: auto;" />';
					} else {
						$preview = '<i class="fa ' . $mime_type[$i["extension"]]["icon"] . ' btn" style="font-size: 128px;"></i>';
					}
				} else {
					$preview = '<img src="./Scarica:?view=true&' . rawurlencode(base64_encode($GLOBALS["dest_token"] . "://" . implode("/", $path) . "/" . str_replace("pdf", "png", $dir))) . '" alt="folder" style="box-shadow: 2px 2px 4px #666; border: #ccc 1px solid; width: 128px; height: auto;" />';
				}
			}
			if(is_dir($file . "/" . $dir) || $i["extension"] !== "png" && file_exists($file . "/" . str_replace("png", "pdf", $dir))) {
				?>
				<div id="<?php print md5($dir); ?>" class="item  col-xs-4 col-lg-4 grid-group-item">
					<div class="panel panel-default text-center">
						<div class="panel-heading">
							<a href="<?php print ((is_dir($file . "/" . $dir)) ? "./Esplora:?" : "./Scheda:?") . rawurlencode(base64_encode($GLOBALS["dest_token"] . "://" . implode("/", $path) . "/" . $dir)); ?>">
								<div class="img"><?php print (strlen($thumbs[0]) > 0) ? $thumb : $preview; ?></div>
							</a>
						</div>
						<div class="panel-heading title lead">
							<a class="btn btn-link list-group-item-heading" style="padding-left: 0;" href="<?php print ((is_dir($file . "/" . $dir)) ? "./Esplora:?" : "./Scheda:?") . rawurlencode(base64_encode($GLOBALS["dest_token"] . "://" . implode("/", $path) . "/" . $dir)); ?>">
								<?php print taglia_stringa($dir, 50); ?>
							</a>
						</div>
						<div class="panel-body clearfix text-left">
							<p class="text-muted left">
								<span class="fa fa-bar-chart-o"></span>&nbsp;&nbsp;<?php $fi = pathinfo($file . "/" . $dir); print strtoupper($fi["extension"]); ?> (<span id="file_mime"><?php print $mime_type[strtolower($fi["extension"])]["mime"]; ?></span>)
							</p>
							<p class="text-muted right">
								<b><?php print $file_size = convert_file_size(trim(@shell_exec("stat -c %s " . special_escapeshellcmd($file . "/" . $dir) . " 2>&1"))); ?></b>
							</p>
						</div>
						<div class="panel-footer clearfix text-left">
							<a class="btn btn-default" href="./Scheda:?<?php print rawurlencode(base64_encode($GLOBALS["dest_token"] . "://" . implode("/", $path) . "/" . $dir)); ?>"><span class="fa fa-tasks"></span>&nbsp;&nbsp;Scheda</a>
							<a class="btn btn-primary right" href="./Scarica:?<?php print rawurlencode(base64_encode($GLOBALS["dest_token"] . "://" . implode("/", $path) . "/" . $dir)); ?>">Scarica&nbsp;&nbsp;<span class="fa fa-cloud-download"></span></a>
						</div>
					</div>
				</div>
				<?php
			}
		}
	}
	?>
</div>