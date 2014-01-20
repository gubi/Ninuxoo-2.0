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
?>
<link rel="stylesheet" href="common/js/jquery.treeview/jquery.treeview.css" type="text/css" media="screen" />
<script type="text/javascript" src="common/js/jquery.treeview/jquery.treeview.js"></script>
<script type="text/javascript" src="common/js/jquery.highlight-4.js"></script>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript" src="common/js/include/local_search.explore.js"></script>

<span style="display: none;" id="result_type">Explore</span>
<span style="display: none;" id="dir_hash"><?php print rawurlencode($hash); ?></span>
<div class="well clearfix">
	<?php
	$path = explode("/", str_replace($GLOBALS["config"]["NAS"]["root_share_dir"], "", $file));
	$b_path = $GLOBALS["config"]["NAS"]["root_share_dir"];
	if($file !== $b_path) {
		$c_path[] = '<a href="./Esplora:?' . rawurlencode($rsa->simple_encrypt($b_path)) . '" title="Vedi al contenuto di questa directory"><span class="fa fa-folder-o"></span></a> ';
		foreach($path as $directory) {
			if($directory !== $filename && strlen($directory) > 0) {
				$b_path .= "/" . $directory;
				$c_path[] = '<a href="./Esplora:?' . rawurlencode($rsa->simple_encrypt(str_replace("//", "/", $b_path))) . '" title="Vedi al contenuto di questa directory">' . $directory . '</a>';
			}
		}
		$c_path[] = $filename;
	} else {
		$c_path[] = '<span class="fa fa-folder-o"></span> Directory principale';
	}
	$complete_path = implode(' <tt>/</tt> ', $c_path);
	?>
	<?php print $complete_path; ?>
        <div class="btn-group right">
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
			if(is_dir($file . "/" . $dir)) {
				$thumbs = glob($file . "/" . $dir . "/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
				$preview = '<i class="fa fa-folder-o btn" style="font-size: 128px;"></i>';
			} else {
				$i = pathinfo($file . "/" . $dir);
				if($mime_type[$i["extension"]]["type"] == "image") {
					$preview = '<img src="./Scarica:?view=true&' . rawurlencode($rsa->simple_encrypt($file . "/" . $dir)) . '" alt="folder" style="width: 128px; height: auto;" />';
				} else {
					$preview = '<i class="fa ' . $mime_type[$i["extension"]]["icon"] . ' btn" style="font-size: 128px;"></i>';
				}
			}
			?>
			<div id="<?php print md5($dir); ?>" class="item  col-xs-4 col-lg-4 grid-group-item">
				<div class="panel panel-default text-center">
					<div class="panel-heading">
						<a href="<?php print ((is_dir($file . "/" . $dir)) ? "./Esplora:?" : "./Scheda:?") . rawurlencode($rsa->simple_encrypt($file . "/" . $dir)); ?>">
							<div class="img"><?php print $preview; ?></div>
						</a>
					</div>
					<div class="panel-heading">
						<a class="btn btn-link list-group-item-heading" href="<?php print ((is_dir($file . "/" . $dir)) ? "./Esplora:?" : "./Scheda:?") . rawurlencode($rsa->simple_encrypt($file . "/" . $dir)); ?>">
							<?php print taglia_stringa($dir, 50); ?>
						</a>
					</div>
					<div class="panel-body clearfix text-left">
						<p class="text-muted"></p>
					</div>
					<div class="panel-footer clearfix text-left">
						<a class="btn btn-default" href="./Scheda:?<?php print rawurlencode($rsa->simple_encrypt($file . "/" . $dir)); ?>"><span class="fa fa-tasks"></span>&nbsp;&nbsp;Scheda</a>
						<a class="btn btn-primary right" href="./Scarica:?<?php print rawurlencode($rsa->simple_encrypt($file . "/" . $dir)); ?>">Scarica&nbsp;&nbsp;<span class="fa fa-cloud-download"></span></a>
					</div>
				</div>
			</div>
			<?php
		}
	}
	?>
</div>