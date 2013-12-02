<?
if(isset($_GET["s"]) && trim($_GET["s"]) !== "") {
	?>
	<div id="breadcrumb">
		<ul>
			<li><a href="./" id="home"></a></li>
			<?php
			if(isset($_GET["q"]) && trim($_GET["q"]) !== "") {
				print '<li><a href="./' . $_GET["s"] . '">' . str_replace("_", " ", $_GET["s"]) . '</a></li>';
				
				if(isset($_GET["id"]) && trim($_GET["id"]) !== "") {
					print '<li><a href="./' . $_GET["s"] . '/' . $_GET["q"] . '">' . str_replace("_", " ", $_GET["q"]) . '</a></li><li>' . str_replace("_", " ", $_GET["id"]) . '</li>';
				} else {
				print '<li>' . str_replace("_", " ", $_GET["q"]) . '</li>';
				}
			} else {
				print '<li>' . str_replace("_", " ", $_GET["s"]) . '</li>';
			}
			?>
		</ul>
	</div>
	<?php
}
?>