<div id="content">
	<div class="menu">
		<div class="group">
			<a href="./Admin/Impostazioni_di_ricerca">
				<img src="common/media/img/admin_panel/nas_search_128_333.png" />
				<p>IMPOSTAZIONI DI RICERCA</p>
				<small>Gestisci le impostazioni di ricerca predefinite per questo NAS</small>
			</a>
			<a href="./Admin/NAS_collegati">
				<img src="common/media/img/admin_panel/linked_nas_128_333.png" />
				<p>NAS COLLEGATI</p>
				<small>Estendi la Rete delle ricerche</small>
			</a>
			<?php
			$db = parse_ini_file("common/include/conf/db.ini", true);
			class dbConnection extends PDO{
				public function __construct() {    
					switch($db["database"]["type"]){
						case "mysql":
							$dbconn = "mysql:host=" . $db["database"]["host"] . ";dbname=" . $db["database"]["db_name"] . ";";
						break;
						case "sqlite":
							$dbconn = "sqlite:" . $db["database"]["host"] . ";";
						break;
						case "postgresql":
							$dbconn = "pgsql:host=" . $db["database"]["host"] . " dbname=" . $db["database"]["db_name"] . ";";
						break;
					}
					parent::__construct($dbconn, $db["database"]["username"], $db["database"]["password"], array(PDO::ATTR_EMULATE_PREPARES => false, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
				}
			}
			try {
				$conn = new dbConnection();
				$connection = true;
			} catch(PDOException $e) {
				$connection = false;
			}
			if($connection) {
				?>
				<a href="./Admin/impostazioni_meteo">
					<img src="common/media/img/admin_panel/meteo_128_333.png" />
					<p>DATI METEO</p>
					<small>Tutti i dati raccolti dalla Stazione</small>
				</a>
				<?php
			}
			?>
			<span class="separator transparent"></span>
			<a href="./Admin/Config_editor">
				<img src="common/media/img/admin_panel/loco_minimal_128_333.png" />
				<p>CONFIG EDITOR</p>
				<small>Le configurazioni di tutti i tuoi device</small>
			</a>
			<a href="./Admin/Sito_locale">
				<img src="common/media/img/admin_panel/website_128_333.png" />
				<p>SITO LOCALE</p>
				<small>Gestisci le pagine e il menu del sito locale</small>
			</a>
			<span class="separator"></span>
			<a href="./Admin/Impostazioni_generali">
				<img src="common/media/img/admin_panel/settings_128_333.png" />
				<br />
				<br />
				<p>IMPOSTAZIONI GENERALI</p>
			</a>
		</div>
		<div class="group">
			<ul style="display: none;">
				<li>Avvia scansione</li>
				<li>Config dei tuoi device</li>
			</ul>
		</div>
	</div>
</div>