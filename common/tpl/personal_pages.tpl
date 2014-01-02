<?php
if(trim($_GET["id"]) == "") {
	require_once("common/include/lib/mime_types.php");
	?>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-file"></span>&nbsp;&nbsp;Pagine personali<small class="help-block"></small></span></div>
		<table class="table" cellpadding="10" cellspacing="10">
			<thead>
				<tr>
					<th>Nome</th><th>Tipo di file</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if(!file_exists("common/md/pages/" . sha1($data[1]))) {
					mkdir("common/md/pages/" . sha1($data[1]));
					chmod("common/md/pages/" . sha1($data[1]), 0777);
				}
				foreach(glob("common/md/pages/" . sha1($data[1]) . "/*.md") as $filename) {
					$file[] = $filename;
				}
				if(count($file) > 0) {
					foreach($file as $filename) {
						$info = pathinfo($filename);
						
						print '<tr><td><a href="./Dashboard/Pagine/' . base64_encode($filename) . '">' . $info["filename"] . '</a></td><td style="color: #999;">' . $mime_type[$info["extension"]] . '</td><td></td></tr>';
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
	$user_config = parse_ini_file("common/include/conf/user/" . sha1($username) . "/user.conf", true);
	
	if($_GET["id"] == "Nuova_pagina") {
		$dir_name = "common/md/pages/" . sha1($data[1]);
		$file_name = '<div class="col-md-8"><label for="page_name">Nome del file della pagina:</label> <input type="text" value="' . $info["filename"] . '" id="page_name" name="page_name" autofocus tabindex="1" />&emsp;<span class="info" id="rename_suggestion" style="display: none;">Sar&agrave; rinominato in "<span></span>"</span><input type="hidden" value="' . $info["filename"] . '" name="original_name" id="original_name" /></div>';
		$remove_btn = "";
	} else {
		$filename = base64_decode($_GET["id"]);
		$info = pathinfo($filename);
		$file_name = (strpos($filename, "/pages/") ? '<div class="col-md-3"><span><label for="page_name">Nome del file della pagina:</label> <input type="text" value="' . $info["filename"] . '" id="page_name" name="page_name" autofocus tabindex="1" /></div><div class="col-md-5"><span class="info" id="rename_suggestion" style="display: none;">Sar&agrave; rinominato in "<span></span>"</span><input type="hidden" value="' . $info["filename"] . '" name="original_name" id="original_name" /></span></div>' : '<b>Nome della pagina:</b> <input type="hidden" value="' . $info["filename"] . '" name="page_name" /><span class="info">' . $info["filename"] . '</span></div>');
		$file = file_get_contents($filename);
		$script_name = '"' . $info["filename"] . '"';
		$dir_name = $info["dirname"];
		$remove_btn = '<button id="remove_btn" class="btn btn-danger" style="margin-right: 10px;">Elimina&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-trash"></span></button>';
	}
	?>
	
	<link href="common/js/chosen/chosen.css" rel="stylesheet" />
	<script type="text/javascript" src="common/js/chosen/chosen.jquery.min.js"></script>
	<script src="common/js/pagedown-bootstrap/js/jquery.pagedown-bootstrap.combined.min.js"></script>
	<link href="common/js/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="common/js/pagedown-bootstrap/css/jquery.pagedown-bootstrap.css" />
	<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
		<script src="common/js/include/common.js"></script>
	<script type="text/javascript" src="common/js/include/personal_pages.js"></script>
	
	<h1>Gestione della pagina <span id="script_name"><?php print $script_name; ?></span></h1>
	<hr />
	<br />
	<form method="post" action="" class="editor_frm" id="editor_frm">
		<div class="row">
			<?php print $file_name; ?>
		</div>
			<tr>
				<td style="padding-top: 10px;">
					<input type="hidden" value="<?php print $username; ?>" name="user_username" id="user_username" />
					<input type="hidden" value="<?php print $dir_name; ?>" name="script_dir" />
					
					<textarea name="page_content" class="form-control" id="page_content" style="width: 100%; height: 450px;" tabindex="2"><?php print $file; ?></textarea>
				</td>
			</tr>
		</table>
	</form>
	<hr />
	<?php print $remove_btn; ?>
	<button class="btn btn-default" id="preview_btn">Anteprima&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-eye-open"></span></button>
	<button class="btn btn-primary right" id="save_editor_btn">Salva&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></span></button>
	<?php
}
?>