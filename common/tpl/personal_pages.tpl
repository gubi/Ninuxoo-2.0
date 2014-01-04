<?php
if(trim($_GET["id"]) == "") {
	require_once("common/include/lib/mime_types.php");
	?>
	<script type="text/javascript" src="common/js/include/personal_pages.js"></script>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-file"></span>&nbsp;&nbsp;Pagine personali<small class="help-block"></small></span></div>
		<table class="table" cellpadding="10" cellspacing="10">
			<thead>
				<tr>
					<th style="width: 20px;"></th>
					<th>Nome</th>
					<th>Tipo di file</th>
				</tr>
			</thead>
			<tbody id="pages_dash">
				<?php
				if(!file_exists("common/md/pages/" . sha1($GLOBALS["username"]))) {
					mkdir("common/md/pages/" . sha1($GLOBALS["username"]));
					chmod("common/md/pages/" . sha1($GLOBALS["username"]), 0777);
				}
				foreach(glob("common/md/pages/" . sha1($GLOBALS["username"]) . "/*.md") as $filename) {
					$file[] = $filename;
				}
				if(count($file) > 0) {
					foreach($file as $filename) {
						$info = pathinfo($filename);
						
						print '<tr><td><input type="hidden" class="script_dir" value="' . $info["dirname"] . '"><input type="hidden" class="page_name" value="' . $info["filename"] . '"><a href="javascript:void(0);" title="Rimuovi questa pagina" class="text-danger remove_notice_btn remove_btn"><span class="glyphicon glyphicon-remove"></span></a></td><td><a href="./Dashboard/Pagine/' . rawurlencode(trim($info["filename"])) . '">' . base64_decode(rawurldecode(trim($info["filename"]))) . '</a></td><td style="color: #999;">' . $mime_type[$info["extension"]] . '</td><td></td></tr>';
					}
				} else {
					print '<tr><td colspan="2" align="center"><span class="info">Nessuna pagina personale salvata</span></td></tr>';
				}
				?>
			</tbody>
		</table>
		<br />
	</div>
	<hr />
	<a class="btn btn-primary right" href="./Dashboard/Pagine/Nuova_pagina">Nuova pagina&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-plus"></span></a>
	<?php
} else {
	$dir_name = "common/md/pages/" . sha1($GLOBALS["username"]);
	if($_GET["id"] !== "Nuova_pagina") {
		$filename = base64_decode(rawurldecode($_GET["id"]));
		$info = pathinfo($filename);
		$file = file_get_contents($dir_name . "/" . rawurlencode(base64_encode($filename)) . ".md");
	}
	?>
	<script src="common/js/pagedown-bootstrap/js/jquery.pagedown-bootstrap.combined.js"></script>
	<link href="common/js/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="common/js/pagedown-bootstrap/css/jquery.pagedown-bootstrap.css" />
	<script type="text/javascript" src="common/js/include/personal_pages.js"></script>
	
	<h1><?php print ($_GET["id"] == "Nuova_pagina") ? "Creazione di una nuova pagina" : $info["filename"]; ?></h1>
	<?php print ($_GET["id"] !== "Nuova_pagina") ? "<h2>Gestione della pagina</h2>" : ""; ?>
	<br />
	<br />
	<form method="post" action="" class="editor_frm form-horizontal" id="editor_frm" onsubmit="save_page(); return false;">
		<div class="row">
			<label for="page_name" class="col-sm-2 control-label">Nome della pagina:</label>
			<div class="col-sm-10">
				<?php
				if($_GET["id"] !== "Nuova_pagina") {
					?>
					<input type="hidden" value="<?php print ($_GET["id"] !== "Nuova_pagina") ? $info["filename"] : ""; ?>" name="original_name" id="original_name" required />
					<?php
				}
				?>
				<input type="text" value="<?php print ($_GET["id"] !== "Nuova_pagina") ? $info["filename"] : ""; ?>" class="form-control" id="page_name" name="page_name" autofocus tabindex="1" required />
			</div>
			</div>
		<br />
		<br />
		<input type="hidden" value="<?php print $GLOBALS["username"]; ?>" name="user_username" id="user_username" />
		<input type="hidden" value="<?php print $dir_name; ?>" name="script_dir" />
		<textarea name="page_content" class="form-control" id="page_content" style="width: 100%; height: 450px;" tabindex="2"><?php print $file; ?></textarea>
		<hr />
		<div id="response" style="display: none;"></div>
		<a class="btn btn-default" href="./Dashboard/Pagine"><span class="glyphicon glyphicon-arrow-left"></span>&nbsp;&nbsp;&nbsp;Torna all'elenco di pagine</a>
		<div class="btn-group right">
			<button class="btn btn-default" id="preview_btn">Anteprima&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-eye-open"></span></button>
			<button type="submit" class="btn btn-primary<?php print ($_GET["id"] !== "Nuova_pagina") ? " disabled" : ""; ?>" id="save_editor_btn">Salva&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></span></button>
		</div>
	</form>
	<?php
}
?>