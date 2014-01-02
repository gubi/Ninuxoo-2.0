<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
	<!-- Brand and toggle get grouped for better mobile display -->
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
	</div>
	<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<?php
		if($has_config) {
			$menu = file_get_contents("common/md/logged_menu.md");
		} else {
			$menu = file_get_contents("common/md/menu.md");
		}
		$menu = str_replace('<ul>', '<ul class="nav navbar-nav">',  Markdown($menu));
		$menu = str_replace("<li></li>", '<li class="divider"></li>', $menu);
		print $menu;
		?>
		<div id="top_menu_right">
			<?php
			if(isset($_GET["s"]) && trim($_GET["s"]) !== "") {
				?>
				<form class="navbar-form navbar-right" role="search" action="./Cerca:">
					<div class="input-group">
						<input type="search" id="search_input" class="form-control" name="q" value="<?php print ((isset($_GET["s"]) && strpos($_GET["s"], "Cerca:") !== false) ? str_replace("Cerca:", "", urldecode($_GET["s"])) : ""); ?>" placeholder="Cerca in Ninuxoo" >
						<input type="submit" value="" style="display: none;">
						<a class="input-group-addon btn btn-default btn-xs" href="./Ricerca_avanzata<?php if(isset($_GET["q"]) && trim($_GET["q"]) !== ""){ print "/Cerca:" . $_GET["q"]; } ?>" title="Ricerca avanzata"><span class="glyphicon glyphicon-cog"></span></a>
					</div>
				</form>
				<?php
			}
			?>
			<ul class="nav navbar-nav navbar-right">
				<?php
				if(isset($_COOKIE["n"])) {
					$data = explode("~", PMA_blowfish_decrypt($_COOKIE["n"], "ninuxoo_cookie"));
					$name = explode(" ", $data[0]);
					?>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php print $name[0]; ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<?php
							if($GLOBALS["is_admin"]) {
								if(trim($_GET["s"]) == "Admin" && !isset($_GET["q"])) {
									?>
									<li class="active"><a href="javascript:void(0);"><span class="fa fa-gears"></span>&nbsp;&nbsp;Admin</a></li>
									<li><a href="./Dashboard"><span class="glyphicon glyphicon-dashboard"></span>&nbsp;&nbsp;Dashboard</a></li>
									<?php
								} else if(trim($_GET["s"]) == "Dashboard" && !isset($_GET["q"])) {
									?>
									<li><a href="./Admin"><span class="fa fa-gears"></span>&nbsp;&nbsp;Admin</a></li>
									<li class="active"><a href="javascript:void(0);"><span class="glyphicon glyphicon-dashboard"></span>&nbsp;&nbsp;Dashboard</a></li>
									<?php
								} else {
									?>
									<li><a href="./Admin"><span class="fa fa-gears"></span>&nbsp;&nbsp;Admin</a></li>
									<li><a href="./Dashboard"><span class="glyphicon glyphicon-dashboard"></span>&nbsp;&nbsp;Dashboard</a></li>
									<?php
								}
							} else {
								if(trim($_GET["s"]) == "Dashboard" && !isset($_GET["q"])) {
									?>
									<li class="active"><a href="javascript:void(0);"><span class="glyphicon glyphicon-dashboard"></span>&nbsp;&nbsp;Dashboard</a></li>
									<?php
								} else {
									?>
									<li><a href="./Dashboard"><span class="glyphicon glyphicon-dashboard"></span>&nbsp;&nbsp;Dashboard</a></li>
									<?php
								}
							}
							?>
							<li class="divider"></li>
							<li><a href="./Dashboard/Chat" id="notify_btn"><span class="fa fa-comments-o"></span>&nbsp;&nbsp;Chat di gruppo</a></li>
							<li class="divider"></li>
							<li><a href="./Esci"><span class="glyphicon glyphicon-log-out"></span>&nbsp;&nbsp;Esci</a></li>
						</ul>
					</li>
					<?php
				} else {
					if(isset($_GET["s"]) && trim($_GET["s"]) == "accedi") {
						?>
						<li><a href="javascript:void(0);"><span class="glyphicon glyphicon-log-in"></span></a>&nbsp;&nbsp;Accedi</li>
						<?php
					} else {
						?>
						<li><a href="./Accedi"><span class="glyphicon glyphicon-log-in"></span>&nbsp;&nbsp;Accedi</a></li>
						<?php
					}
				}
				?>
			</ul>
		</div>
	</div>
</nav>