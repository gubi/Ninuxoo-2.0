<?php
if(trim($_GET["id"]) == "Pagine") {
	require_once("common/include/lib/mime_types.php");
	?>
	<h1>Gestione delle pagine</h1>
	<br />
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-file"></span>&nbsp;&nbsp;Pagine salvate<small class="help-block"></small></span></div>
		<table class="table" cellpadding="10" cellspacing="10">
			<thead>
				<tr>
					<th>Nome</th><th>Tipo di file</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach(glob("common/md/pages/*.md") as $filename) {
					$file[] = $filename;
				}
				if(count($file) > 0) {
					foreach($file as $filename) {
						$info = pathinfo($filename);
						
						print '<tr><td><a href="./Admin/Sito_locale/' . base64_encode($filename) . '">' . $info["filename"] . '</a></td><td style="color: #999;">' . $mime_type[$info["extension"]] . '</td></tr>';
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
	<a class="btn btn-primary right" href="./Admin/Sito_locale/Nuova_pagina">Nuova pagina&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-plus"></a>
	<?php
} else {
	$user_config = parse_ini_file("common/include/conf/user/" . sha1($username) . "/user.conf", true);
	
	if($_GET["id"] == "Nuova_pagina") {
		$dir_name = "common/md/pages";
		$file_name = '<div class="col-md-8"><label for="page_name">Nome della pagina:</label> <input type="text" value="' . $info["filename"] . '" style="width: 30%;" id="page_name" name="page_name" autofocus tabindex="1" />&emsp;<span class="info" id="rename_suggestion" style="display: none;">Sar&agrave; rinominato in "<span></span>"</span><input type="hidden" value="' . $info["filename"] . '" name="original_name" id="original_name" /></span></div>';
		$remove_btn = "";
	} else {
		$filename = base64_decode($_GET["id"]);
		$info = pathinfo($filename);
		$file_name = (strpos($filename, "/pages/") ? '<div class="col-md-3"><label for="page_name">Nome della pagina:</label> <input type="text" value="' . $info["filename"] . '" style="width: 30%;" id="page_name" name="page_name" autofocus tabindex="1" /></div><div class="col-md-5"><span class="info" id="rename_suggestion" style="display: none;">Sar&agrave; rinominato in "<span></span>"</span><input type="hidden" value="' . $info["filename"] . '" name="original_name" id="original_name" /></span></div>' : '<b>Nome della pagina:</b> <input type="hidden" value="' . $info["filename"] . '" name="page_name" /><span class="info">' . $info["filename"] . '</span></div>');
		$file = file_get_contents($filename);
		$script_name = '"' . $info["filename"] . '"';
		$dir_name = $info["dirname"];
		$remove_btn = '<button id="remove_btn" class="btn btn-danger" style="margin-right: 10px;">Elimina&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-trash"></span></button>';
	}
	
	if($user_config["User"]["use_editor_always"] == "true") {
		$themes_select = str_replace('<option>' . $user_config["User"]["editor_theme"] . '</option>', '<option selected="selected">' . $user_config["User"]["editor_theme"] . '</option>', '<div class="col-md-4"><div class="right">Tema dell\'editor: <select id="code_theme" style="width: 200px;"><option>default</option><option>3024-day</option><option>3024-night</option><option>ambiance</option><option>base16-dark</option><option>base16-light</option><option>blackboard</option><option>cobalt</option><option>eclipse</option><option>elegant</option><option>erlang-dark</option><option>lesser-dark</option><option>mbo</option><option>midnight</option><option>monokai</option><option>neat</option><option>night</option><option>paraiso-dark</option><option>paraiso-light</option><option>rubyblue</option><option>solarized dark</option><option>solarized light</option><option>the-matrix</option><option>tomorrow-night-eighties</option><option>twilight</option><option>vibrant-ink</option><option>xq-dark</option><option>xq-light</option></select></div></div>');
		$btn_preview = "";
		?>
		<link href="common/js/chosen/chosen-bootstrap.css" rel="stylesheet" />
		<script type="text/javascript" src="common/js/chosen/chosen.jquery.js"></script>
		<?php require_once("common/tpl/scripts.codemirror.tpl"); ?>
		<script type="text/javascript" src="common/js/include/local_site.pages.codemirror.js"></script>
		<?php
	} else {
		$themes_select = "";
		$btn_preview = '<br /><button class="btn btn-default preview_btn">Anteprima&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-eye-open"></span></button>';
		?>
		<script src="common/js/pagedown-bootstrap/js/jquery.pagedown-bootstrap.combined.min.js"></script>
		<link href="common/js/font-awesome/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="common/js/pagedown-bootstrap/css/jquery.pagedown-bootstrap.css" />
		<script type="text/javascript" src="common/js/include/local_site.pages.pagedown.js"></script>
		<?php
	}
	?>
	<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
		<script src="common/js/include/page_editor.js"></script>
	<script src="common/js/include/common.js"></script>
	<script src="common/js/include/local_site.pages.common.js"></script>
	
	<h1>Gestione della pagina <span id="script_name"><?php print $script_name; ?></span></h1>
	<br />
	<div class="well">
		<p><?php require_once("common/tpl/editor_status.tpl"); ?></p>
	</div>
	<?php print $btn_preview; ?>
	<hr />
	<form method="post" action="" class="editor_frm" id="editor_frm">
		<div class="row">
			<?php print $file_name . $themes_select; ?>
		</div>
		<br />
		<input type="hidden" value="<?php print $username; ?>" name="user_username" id="user_username" />
		<input type="hidden" value="<?php print $dir_name; ?>" name="script_dir" />
		<textarea name="script" id="script" style="width: 100%; height: 450px;" tabindex="2"><?php print $file; ?></textarea>
	</form>
	<hr />
	<?php print $remove_btn; ?>
	<button class="btn btn-primary right" id="save_page_btn">Salva&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></span></button>
	<?php
}
?>