<?php
header("Content-type: text/plain; charset=utf-8");

class scan {
	function __construct() {
		$this->class_dir = __DIR__;
		$this->dir = str_replace("classes", "conf", $this->class_dir);
	}
	private function parse_ini($file) {
		return parse_ini_file($this->dir . "/" . $file, true);
	}
	public function start_time() {
		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		$mtime = $mtime[1] + $mtime[0];
		return $mtime;
	}
	public function end_time($startime) {
		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		$mtime = $mtime[1] + $mtime[0];
		return round($mtime - $startime, 5);
	}
	public function text_header() {
		$header = str_repeat("-", 100) . "\n";
		$header .= "# NinuXoo Local scanning \n";
		$header .= "# " . date("Y-m-d H:i:s") . "\n";
		$header .= str_repeat("-", 100) . "\n";
		$header .= "Start scanning...\n";
		
		return $header;
	}
	private function statistics($curr_dir) {
		$curr = implode("\n -> ", $curr_dir);
		$stats = "\n" . count($curr_dir) . " directories in shares:\n";
		$stats .= " -> " . $curr . "\n\n";
		
		return $stats;
	}
	private function output($type = "", $stats = "", $end_time = "", $share_dir) {
		$config = $this->get_config();
		
		switch($type) {
			case "ajax":
				return " done.\n";
				break;
			case "json":
				return json_encode(array("data" => array("date" => date("Y-m-d"), "elapsed_time" => $end_time, "files_count" => count($share_dir))));
				break;
			default:
				$output = $this->text_header();
				$output .= $stats;
				$output .= count($share_dir) . " files listed in: " . $config["NAS"]["listing_file_dir"] . "listing\n";
				$output .= "listing file crypted by RSA 2048 bit.\n\n";
				$output .= "Elapsed time: " . $end_time . " seconds\n";
				$output .= str_repeat("-", 100) . "\n";
				$output .= "Goodbye.\n";
				
				return $output;
				break;
		}
	}
	
	private function get_config() {
		if (!file_exists($this->dir . "/config.ini")) {
			return "error: no `config.ini` file.\nPlease, run setup before.\n\nNothing to scan.\nExit";
			exit();
		} else {
			return $this->parse_ini("config.ini");
		}
	}
	private function smb_conf_file() {
		$get_config = $this->get_config();
		return trim(preg_replace("/;(.*?)$/i", "", $get_config["NAS"]["root_share_dir"])) . "smb.conf";
	}
	private function parse_smb_conf() {
		if (!class_exists("manage_conf_file", false)) {
			require($this->class_dir . "/manage_conf_file.class.php");
		}
		$conf = new manage_conf_file();
		return $conf->parse($this->smb_conf_file());
	}
	private function update_config($startime) {
		if (!class_exists("manage_conf_file", false)) {
			require($this->class_dir . "/manage_conf_file.class.php");
		}
		$end_time = $this->end_time($startime);
		$conf = new manage_conf_file();
		$conf->conf_replace("last_scan_date", date("Y-m-d"), $this->dir . "/config.ini");
		$conf->conf_replace("last_items_count", count(explode("\n", $this->listing)), $this->dir . "/config.ini");
		$conf->conf_replace("last_scanning_time", $end_time, $this->dir . "/config.ini");
		
		return $end_time;
	}
	private function scan() {
		$get_config = $this->get_config();
		foreach($get_config["NAS"]["nas_shares"] as $scan_dir){
			$share_ = array();
			
			$splitted_dir = array_values(array_filter(explode("/", trim(str_replace("./", "", str_replace("//", "/", $get_config["NAS"]["root_share_dir"] . "/") . $scan_dir)))));
			$this->listing .= shell_exec("find " . str_replace(" ", "\ ", escapeshellcmd(str_replace("//", "/", $get_config["NAS"]["root_share_dir"] . "/") . $scan_dir)) . " -mindepth 1 | sort");
		}
		return $this->listing;
	}
	public function save($type = "") {
		if (!class_exists("rsa", false)) {
			require($this->class_dir . "/rsa.class.php");
		}
		
		$startime = $this->start_time();
		$get_config = $this->get_config();
		
		$scan = $this->scan();
		sort($get_config["NAS"]["nas_shares"]);
		foreach($get_config["NAS"]["nas_shares"] as $f) {
			$info = pathinfo($f);
			$shares[] = $info["basename"];
			$scans = str_replace("//", "/", str_replace($get_config["NAS"]["root_share_dir"], "", $scan));
		}
		/*
		SAVE LISTING
		*/
		$listing_file = fopen($get_config["NAS"]["listing_file_dir"] . "/listing", "w+");
		fwrite($listing_file, $scans);
		fclose($listing_file);
		@chmod($get_config["NAS"]["listing_file_dir"] . "/listing", 0777);
		
		$end_time = $this->update_config($startime);
		$stats = $this->statistics($shares);
		print $this->output($type, $stats, $end_time, explode("\n", $scans));
	}
	
}
?>
