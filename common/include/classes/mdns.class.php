<?php

class mdns {
	private function include_path() {
		return str_replace("classes", "", realpath(dirname(__FILE__)));
	}
	public function check_trusted() {
		foreach(glob($this->include_path() . "conf/trusted/*.pem") as $filename) {
			$trusted[] = str_replace(array($this->include_path() . "conf/trusted/", ".pem"), "", $filename);
		}
		return $trusted;
	}
	public function check_untrusted() {
		foreach(glob($this->include_path() . "conf/untrusted/*.pem") as $filename) {
			$untrusted[] = str_replace(array($this->include_path() . "conf/untrusted/", ".pem"), "", $filename);
		}
		return $untrusted;
	}
	private function get_owner($key) {
		require_once($this->include_path() . "lib/simplehtmldom_1_5/simple_html_dom.php");
		
		$mit = "http://pgp.mit.edu:11371";
		if($own = @file_get_html($mit . "/pks/lookup?search=0x" . trim($key, '"'))) {
			$ret = $own->find("a");
			$owner = array();
			foreach($ret as $a) {
				if(strpos($a->href, "lookup") !== false) {
					$owner[] = $a->plaintext;
				}
			}
			$o["name"] = trim(preg_replace("/&lt;(.*?)&gt;/i", "", $owner[1]));
			$o["email"] = trim(preg_replace("/.*?&lt;|&gt;/i", "", $owner[1]));
			return $o;
		}
	}
	private function get_address($out, $hostname) {
		foreach($out as $kp => $parsed) {
			$o = explode(";", $parsed);
			if(stripcslashes($o[2]) == $hostname) {
				if($o[1] == "IPv6") {
					$o[6] = "[" . $o[6] . "]";
				}
				$oo[] = $o[6];
			}
		}
		return $oo;
	}
	public function scan($get_owner = false, $filter = "") {
		$avh = shell_exec("avahi-browse _dns-sd._udp -prt");
		preg_match_all("/\=\;(.*?)\n/is", $avh, $out);
		
		foreach($out[1] as $kp => $parsed) {
			$o = explode(";", $parsed);
			$msg = explode(":", $o[8]);
			if (is_array($msg)) {
				$message = explode("::", base64_decode($msg[1]));
				if (is_array($message) && trim($message[1]) !== "") {
					if($filter == ""){
						$checkt = "";
					} else {
						$checkt = $filter;
					}
					if($filter == $checkt) {
						if(in_array("" . $message[2], $this->check_trusted())) {
							$status = "trusted";
						} else if(is_array($untrusted) && in_array("" . $message[2], $this->check_trusted())) {
							$status = "untrusted";
						} else {
							$status = "unknown";
						}
						if($o[3] == "_dns-sd._udp" && $o[7] == 64689 && trim($msg[0], '"') == "Hello guys I'm a Ninuxoo device") {
							$ips = explode(" ", shell_exec("hostname  -I"));
							
							if(!in_array($o[6], $ips)) {
								if($get_owner == true) {
									$ndata[stripcslashes($o[2])]["owner"] = $this->get_owner($message[0]);
								}
								$ndata[stripcslashes($o[2])]["owner"]["key"] = trim($message[0], '"');
								$ndata[stripcslashes($o[2])]["geo_zone"] = trim($message[1]);
								$ndata[stripcslashes($o[2])]["reachability"][] = stripcslashes($o[1]);
								$ndata[stripcslashes($o[2])]["token"] = base64_encode($message[2]);
								$ndata[stripcslashes($o[2])]["status"] = $status;
								$ndata[stripcslashes($o[2])]["address"] = $this->get_address($out[1], stripcslashes($o[2]));
							}
						}
					} else {
						$ndata = "error::This token unexist!";
					}
				}
			}
		}
		return $ndata;
	}
}
?>