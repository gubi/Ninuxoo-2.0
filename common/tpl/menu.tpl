<div id="top_menu">
	<div>
		<?php
		if($has_config) {
			$menu = file_get_contents("common/md/logged_menu.md");
		} else {
			$menu = file_get_contents("common/md/menu.md");
		}
		print str_replace("<li></li>", '<li class="separator">&nbsp;</li>', Markdown($menu));
		?>
		<ul>
			<?php
			if(isset($_COOKIE["n"])) {
				$data = explode("~", PMA_blowfish_decrypt($_COOKIE["n"], "ninuxoo_cookie"));
				if(trim($_GET["s"]) == "Admin" && !isset($_GET["q"])) {
					?>
					<li><a href="javascript:void(0);"><?php print $data[0]; ?></a></li>
					<li class="separator">&nbsp;</li>
					<li><a href="./Esci">Esci</a></li>
					<?php
				} else {
					?>
					<li><a href="./Admin"><?php print $data[0]; ?></a></li>
					<li class="separator">&nbsp;</li>
					<li><a href="./Esci">Esci</a></li>
					<?php
				}
			} else {
				if(isset($_GET["s"]) && trim($_GET["s"]) == "accedi") {
					?>
					<li><a href="javascript:void(0);">Accedi</a></li>
					<?php
				} else {
					?>
					<li><a href="./Accedi">Accedi</a></li>
					<?php
				}
			}
			?>
		</ul>
	</div>
</div>