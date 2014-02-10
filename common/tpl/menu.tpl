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
			if(isset($_COOKIE["n"])) {
				?>
				<ul class="nav navbar-nav navbar-right">
					<li><a href="javascript:void(0);" id="chat_btn" class="<?php print ($GLOBALS["user_settings"]["Notification"]["new_chat_messages"] == "true") ? "notify" : ""; ?>" style="padding-left: 5px;" title="<?php print ($GLOBALS["user_settings"]["Chat"]["panel_status"] == "open") ? "Chiudi il pannello delle chat" : "Apri il pannello delle chat"; ?>"><span class="arrow"><span class="fa fa-angle-left" style="padding-right: 5px;"></span></span><span class="fa fa-comment fa-flip-horizontal"></span><span class="arrow"><span class="fa fa-angle-right" style="display: none;"></span></span></a></li>
				</ul>
				<?php
			}
			if(isset($_GET["s"]) && trim($_GET["s"]) !== "") {
				?>
				<form class="navbar-form navbar-right" role="search" method="get" action="">
					<div class="input-group">
						<div class="input-group merged-xs">
							<label for="search_input" class="input-group-addon btn-default btn-xs"><span class="fa fa-search"></span></label>
							<input type="search" id="search_input" class="form-control" name="c" value="<?php print $GLOBALS["search_term"]; ?>" placeholder="Cerca in Ninuxoo" >
						</div>
						<input type="submit" name="search" value="" style="display: none;">
						<a class="input-group-addon btn btn-xs" href="./Ricerca_avanzata/Cerca:<?php print $GLOBALS["search_term"]; ?>" title="Ricerca avanzata"><span class="glyphicon glyphicon-cog"></span></a>
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
							<li><a href="./Dashboard/Notifiche_di_gruppo" id="notify_btn" class="<?php print ($GLOBALS["user_settings"]["Notification"]["new_chat_messages"] == "true") ? "notify" : ""; ?>"><span class="fa fa-comments-o"></span>&nbsp;&nbsp;Notifiche di gruppo</a></li>
							<li class="divider"></li>
							<li><a href="./Esci"><span class="glyphicon glyphicon-log-out"></span>&nbsp;&nbsp;&nbsp;Esci</a></li>
						</ul>
					</li>
					<?php
				} else {
					if($has_config) {
						if(isset($_GET["s"]) && trim($_GET["s"]) == "accedi") {
							?>
							<li><a href="javascript:void(0);"><span class="glyphicon glyphicon-log-in"></span></a><span id="login_btn_txt">&nbsp;&nbsp;Accedi</span></li>
							<?php
						} else {
							?>
							<li><a href="./Accedi"><span class="glyphicon glyphicon-log-in"></span><span id="login_btn_txt">&nbsp;&nbsp;Accedi</span></a></li>
							<?php
						}
					}
				}
				?>
			</ul>
		</div>
	</div>
</nav>