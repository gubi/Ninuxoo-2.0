<?php
if(isset($_GET["id"]) && trim($_GET["id"]) !== "") {
	if($_GET["id"] == "Menu") {
		require_once("local_site.menu.tpl");
	} else {
		require_once("local_site.pages.tpl");
	}
} else {
	?>
	<div id="content">
		<div class="menu">
			<div class="group">
				<a href="./Admin/Sito_locale/Menu">
					<img src="common/media/img/admin_panel/tab_128_333.png" />
					<p>MENU</p>
					<small>Gestione del menu nella barra superiore</small>
				</a>
				<a href="./Admin/Sito_locale/Pagine">
					<img src="common/media/img/admin_panel/document_text_128_333.png" />
					<p>PAGINE</p>
					<small>Gestione dei contenuti delle pagine</small>
				</a>
			</div>
		</div>
	</div>
	<?php
}
?>