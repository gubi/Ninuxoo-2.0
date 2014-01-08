<?php
$logged_users_menu = file_get_contents("common/md/logged_menu.md");
$users_menu = file_get_contents("common/md/menu.md");
$user_config = parse_ini_file("common/include/conf/user/" . sha1($username) . "/user.conf", true);

if($user_config["User"]["use_editor_always"] == "true") {
	$themes_select = str_replace('<option>' . $user_config["User"]["editor_theme"] . '</option>', '<option selected="selected">' . $user_config["User"]["editor_theme"] . '</option>', '<div class="right"><label for="code_theme">Tema degli editor:</label> <select id="code_theme" style="width: 200px;"><option>default</option><option>3024-day</option><option>3024-night</option><option>ambiance</option><option>base16-dark</option><option>base16-light</option><option>blackboard</option><option>cobalt</option><option>eclipse</option><option>elegant</option><option>erlang-dark</option><option>lesser-dark</option><option>mbo</option><option>midnight</option><option>monokai</option><option>neat</option><option>night</option><option>paraiso-dark</option><option>paraiso-light</option><option>rubyblue</option><option>solarized dark</option><option>solarized light</option><option>the-matrix</option><option>tomorrow-night-eighties</option><option>twilight</option><option>vibrant-ink</option><option>xq-dark</option><option>xq-light</option></select></div>');
	$btn_preview = "";
	?>
	<link href="common/js/chosen/chosen-bootstrap.css" rel="stylesheet" />
	<script type="text/javascript" src="common/js/chosen/chosen.jquery.js"></script>
	<?php require_once("common/tpl/scripts.codemirror.tpl"); ?>
	<script type="text/javascript" src="common/js/include/local_site.menu.codemirror.js"></script>
	<?php
} else {
	$themes_select = "";
	$btn_preview = '<br /><button class="btn btn-default preview_btn">Anteprima&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-eye-open"></span></button>';
	?>
	<script src="common/js/pagedown-bootstrap/js/jquery.pagedown-bootstrap.combined.min.js"></script>
	<link href="common/js/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<link rel="stylesheet" type="text/css" href="common/js/pagedown-bootstrap/css/jquery.pagedown-bootstrap.css" />
	<script type="text/javascript" src="common/js/include/local_site.menu.pagedown.js"></script>
	<?php
}
?>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript" src="common/js/include/local_site.menu.common.js"></script>

<h1>Gestione del menu superiore</h1>
<br />
<div class="well">
	<p><?php require_once("common/tpl/editor_status.tpl"); ?></p>
	<p>Per collegare una voce del menu ad una nuova pagina &egrave; sufficiente creare la pagina con lo stesso nome.<br />
	Ad esempio il menu "<tt>* [Test](./Test)</tt>" sar&agrave; collegato alla pagina con nome "<tt>Test</tt>"</p>
</div>
<?php print $btn_preview; ?>
<hr />
<form method="post" action="" class="editor_frm" id="editor_frm">
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary">Utenti collegati<small class="help-block">Menu visibile agli utenti registrati dopo aver fatto il login</small></span></div>
		<div class="panel-body">
			<input type="hidden" value="<?php print $username; ?>" name="user_username" id="user_username" />
			
			<?php print $themes_select; ?>
			<textarea name="logged_menu" id="logged_menu" class="editor" style="width: 99%; height: 200px;"><?php print $logged_users_menu; ?></textarea>
		</div>
	</div>
	<br />
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary">Utenti generici<small class="help-block">Menu visibile a chiunque</small></span></div>
		<div class="panel-body">
			<textarea name="menu" id="menu" class="editor" style="width: 99%; height: 200px;"><?php print $users_menu; ?></textarea>
		</div>
	</div>
</form>
<hr />
<?php print $btn_preview; ?>
<button class="btn btn-primary right" id="save_menu_btn">Salva&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></span></button>