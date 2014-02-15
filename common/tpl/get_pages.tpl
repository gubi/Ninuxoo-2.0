<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script src="common/js/include/common.js"></script>
<script src="common/js/include/users_pages.js"></script>

<?php
if(!isset($_GET["q"]) || trim($_GET["q"]) == "") {
	?>
	<script type="text/javascript">
	$(document).ready(function() {
		$.get_user_pages();
	});
	</script>
	<div class="panel panel-default">
		<div class="panel-heading">
			<span class="lead text-primary"><span class="fa fa-users"></span>&nbsp;&nbsp;Utenti con pagine condivise<small class="help-block">Utenti che hanno creato e condiviso pagine</small></span>
		</div>
		<div class="panel-body">
			<div id="users_pages"></div>
		</div>
	</div>
<?php
} else {
	if(!isset($_GET["id"]) || trim($_GET["id"]) == "") {
		?>
		<script type="text/javascript">
		$(document).ready(function() {
			$.get_user_pages('<?php print trim($_GET["q"]); ?>');
		});
		</script>
		<div class="panel panel-default">
			<div class="panel-heading">
				<span class="lead text-primary"><span class="fa fa-users"></span>&nbsp;&nbsp;Utenti con pagine condivise<small class="help-block">Utenti che hanno creato e condiviso pagine</small></span>
			</div>
			<div class="panel-body">
				<div id="users_pages"></div>
			</div>
		</div>
		<?php
	} else {
		if(file_exists("common/md/pages/" . addslashes(trim($_GET["q"])) . "/" . addslashes(rawurlencode(trim($_GET["id"]))) . ".md")) {
			$page = file_get_contents("common/md/pages/" . addslashes(trim($_GET["q"])) . "/" . addslashes(rawurlencode(trim($_GET["id"]))) . ".md");
			print '<div>' . str_replace('href="http', 'target="_blank" href="http', Markdown($page)) . '</div>';
		} else {
			require_once("common/tpl/404.tpl");
		}
	}
}
?>