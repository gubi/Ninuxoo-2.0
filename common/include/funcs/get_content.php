<div id="content">
	<?php
	if($has_config) {
		if(isset($_GET["s"]) && trim($_GET["s"]) !== "") {
			switch(strtolower($_GET["s"])) {
				case "accedi";
					include("common/tpl/login.tpl");
					break;
				case "admin":
					if(isset($_COOKIE["n"])) {
						switch(strtolower($_GET["q"])) {
							case "config_editor":
								include("common/tpl/config_editor.tpl");
								break;
							case "impostazioni_di_ricerca":
								include("common/tpl/search_settings.tpl");
								break;
							case "impostazioni_generali":
								include("common/tpl/general_settings.tpl");
								break;
							case "impostazioni_meteo":
								include("common/tpl/meteo_settings.tpl");
								break;
							case "nas_collegati":
								include("common/tpl/linked_nas.tpl");
								break;
							case "sito_locale":
								include("common/tpl/local_site.tpl");
								break;
							default:
								include("common/tpl/admin_panel.tpl");
								break;
						}
					} else {
						include("common/tpl/login.tpl");
					}
					break;
				default:
					include("common/tpl/load_page.tpl");
					break;
			}		
		} else {
			require_once("common/tpl/content.tpl");
		}
	} else {
		include("common/include/funcs/_ajax/check_internet_status.php");
		$btn_next_disabled = (check_internet_status() == "ok") ? "" : check_internet_status();
		require_once("common/tpl/setup.tpl");
	}
	?>
</div>