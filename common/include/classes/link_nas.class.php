<?php

class link_nas {
	public function __construct() {
		require_once(realpath(dirname(__FILE__)) . "/logging.class.php");
		require_once(realpath(dirname(__FILE__)) . "/rsa.class.php");
		require_once(str_replace("classes", "", realpath(dirname(__FILE__))) . "/funcs/_blowfish.php");
		
		$this->main_dir = str_replace("classes", "", realpath(dirname(__FILE__))) . "conf/";
		$this->trusted_dir = $this->main_dir . "trusted/";
		
		$this->log = new Logging();
		if(!file_exists(str_replace("classes", "", realpath(dirname(__FILE__))) . "log/ninuxoo.log")) {
			$fl = fopen(str_replace("classes", "", realpath(dirname(__FILE__))) . "log/ninuxoo.log", "wb");
			fwrite($fl, "");
			fclose($fl);
		}
		$this->log->file(str_replace("classes", "", realpath(dirname(__FILE__))) . "log/ninuxoo.log");
		$this->rsa = new rsa();
		
	}
	public function start_request() {
		// B1 request
		$fb = fopen($this->main_dir . "rsa_2048_pub.pem", "r");
		$Bkey = fread($fb, 8192);
		fclose($fb);
		
		$limit = (isset($_GET["debug"]) ? false : date("Y-m-d H:i:s"));
		$time_limit = $this->rsa->get_time_limit($limit);
		
		$this->log->write("notice", "[B1] Started with time limit of " . $time_limit . " seconds");
		
		$friendship_request = PMA_blowfish_encrypt(trim($Bkey), $this->rsa->get_time_limit($limit, true));
		$Brequest = PMA_blowfish_encrypt($friendship_request . "::::" . $this->rsa->get_time_limit($limit, true), "Shoot some hoops?");
		
		$this->log->write("notice", "[B1] Time limit is fixed at " . $this->rsa->get_time_limit($limit, true));
		$this->log->write("notice", "[B1] Request is:\n" . rawurlencode($Brequest));
		
		return rawurlencode($Brequest);
		$this->log->close();
	}
	public function first_response($request) {
		// A1 parse
		$friendship_request = explode("::::", PMA_blowfish_decrypt(rawurldecode($request), "Shoot some hoops?"));
		$time_limit = $friendship_request[1];
		
		if($this->rsa->get_time_limit($time_limit)) {
			$this->log->write("notice", "[A1] Started with deadline " . $this->rsa->get_time_limit($time_limit, true));
			
			$resp = PMA_blowfish_decrypt($friendship_request[0], $time_limit);
			$Bkey = $resp;
			
			// A response
			$fb = fopen($this->main_dir . "rsa_2048_pub.pem", "r");
			$Akey = fread($fb, 8192);
			fclose($fb);
			
			$Atoken = $this->rsa->get_token($Akey);
			$Btoken = $this->rsa->get_token($Bkey);
			$this->log->write("notice", "[A1] Public key received with token " . $Btoken);
			
			// Check if is already trusted
			foreach(glob($this->trusted_dir . "*.pem") as $filename) {
				$ctokens[] = str_replace(array($this->trusted_dir, ".pem"), "", $filename);
			}
			if(is_array($ctokens)) {
				$this->log->write("notice", "[A1] Check if '" . $Btoken . "' is already trusted...");
				if(in_array($Btoken, $ctokens)) {
					$exists = true;
					$this->log->write("notice", "[A1] " . $Btoken . " is already trusted");
				} else {
					$exists = false;
				}
			} else {
				$exists = false;
				$this->log->write("error", "[A1] " . $Btoken . " is no knower and no trusted. Aborting... It will need to repeat all process");
			}
			if(!$exists) {
				$knower = fopen($this->trusted_dir . $Btoken . ".pem~", "wb");
				$this->log->write("notice", "[A1] NAS " . $Btoken . " added to '" . $this->trusted_dir . $Btoken . ".pem~' as KNOWER");
				fwrite($knower, $Bkey);
				fclose($knower);
				chmod($this->trusted_dir . $Btoken . ".pem~", 0777);
			} else {
				print "Already trusted";
				exit();
			}
			
			$encrypted_A_response = $this->rsa->public_encrypt($this->main_dir, "trusted/"  . $Atoken . ".pem~", "rsa_2048_pub.pem", $time_limit);
			if($encrypted_A_response) {
				$this->log->write("notice", "[A1] Response for B2 is:\n" . rawurlencode($encrypted_A_response));
				return rawurlencode($encrypted_A_response);
			} else {
				$this->log->write("error", "[A1] Time is expired. Deadline was " . $this->rsa->get_time_limit($time_limit, true) . " (now is " . date("Y-m-d H:i:s") . ")");
				$this->rsa->get_time_error();
			}
		} else {
			$this->log->write("error", "[A1] Time is expired. Deadline was " . $this->rsa->get_time_limit($time_limit, true) . " (now is " . date("Y-m-d H:i:s") . ")");
			$this->rsa->get_time_error();
		}
		$this->log->close();
	}
	public function second_response($first_response) {
		// B2 parse
		$key = explode("::::", rawurldecode($first_response));
		
		if(count($key) > 1) {
			if($this->rsa->get_time_limit($this->rsa->simple_decrypt($key[2]))) {
				$Akey = $this->rsa->private_decrypt($this->main_dir, "rsa_2048_priv.pem", $key[1], $key[0], $this->rsa->simple_decrypt($key[2]));
				if($Akey) {
					$this->log->write("notice", "[B2] Started with deadline " . $this->rsa->get_time_limit($this->rsa->simple_decrypt($key[2]), true));
					$Atoken = $this->rsa->get_token($Akey);
					$this->log->write("notice", "[B2] Public key received with token " . $Atoken);
					$trusted = fopen($this->trusted_dir . $Atoken . ".pem", "wb");
					fwrite($trusted, $Akey);
					fclose($trusted);
					chmod($this->trusted_dir . $Atoken . ".pem~", 0777);
					$this->log->write("notice", "[B2] NAS " . $Atoken . " added to '" . $this->trusted_dir . $Atoken . ".pem' as TRUSTED");
					
					$encrypted_B_response = $this->rsa->public_encrypt($this->main_dir, "trusted/"  . $Atoken . ".pem", "rsa_2048_pub.pem", $this->rsa->simple_decrypt($key[2]));
					if($encrypted_B_response) {
						$this->log->write("notice", "[B2] Response for A2 is:\n" . rawurlencode($encrypted_B_response));
						return rawurlencode($encrypted_B_response);
					} else {
						$this->log->write("error", "[B2] Time is expired. Deadline was " . $this->rsa->get_time_limit($this->rsa->simple_decrypt($key[2]), true) . " (now is " . date("Y-m-d H:i:s") . ")");
						$this->rsa->get_time_error();
					}
				} else {
					$this->log->write("error", "[B2] Time is expired. Deadline was " . $this->rsa->get_time_limit($this->rsa->simple_decrypt($key[2]), true) . " (now is " . date("Y-m-d H:i:s") . ")");
					$this->rsa->get_time_error();
				}
			} else {
				$this->log->write("error", "[B2] Time is expired. Deadline was " . $this->rsa->get_time_limit($this->rsa->simple_decrypt($key[2]), true) . " (now is " . date("Y-m-d H:i:s") . ")");
				$this->rsa->get_time_error();
			}
		} else {
			$this->log->write("error", "Can't explode 'confirm_friend' request. Probably time expired for B2...");
			$this->rsa->get_time_error();
		}
		$this->log->close();
	}
	
	public function third_response($second_response) {
		// A2 parse
		$key = explode("::::", rawurldecode($second_response));
		if(count($key) > 1) {
			if($this->rsa->get_time_limit($this->rsa->simple_decrypt($key[2]))) {
				$Bkey = $this->rsa->private_decrypt($this->main_dir, "rsa_2048_priv.pem", $key[1], $key[0], $this->rsa->simple_decrypt($key[2]));
				if($Bkey) {
					$this->log->write("notice", "[A2] Started with deadline " . $this->rsa->get_time_limit($this->rsa->simple_decrypt($key[2]), true));
					$Btoken = $this->rsa->get_token($Bkey);
					$this->log->write("notice", "[A2] Public key received with token " . $Btoken);
					return "Hello " . $Btoken . ", nice to meet you!";
					
					// Check knowers
					foreach(glob($this->trusted_dir . "*.pem~") as $filename) {
						$ktokens[] = str_replace(array($this->trusted_dir, ".pem~"), "", $filename);
					}
					if(is_array($ktokens)) {
						$this->log->write("notice", "[A2] " . count($ktokens) . " knowers in database");
						if(in_array($Btoken, $ktokens)) {
							// Check_keys
							$fb = fopen($this->trusted_dir . $Btoken . ".pem~", "r");
							$knowerBkey = fread($fb, 8192);
							fclose($fb);
							// Rename knower to trusted
							if(trim($knowerBkey) == trim($Bkey)) {
								rename($this->trusted_dir . $Btoken . ".pem~", $this->trusted_dir . $Btoken . ".pem");
								$this->log->write("notice", "[A2] NAS " . $Btoken . " added to '" . $this->trusted_dir . $Btoken . ".pem' as TRUSTED");
							}
						}
					} else {
						$this->log->write("notice", "[A2] There's no knowers in the database.");
						
						// Check trusted
						foreach(glob($this->trusted_dir . "*.pem") as $filename) {
							$ttokens[] = str_replace(array($this->trusted_dir, ".pem"), "", $filename);
						}
						if(is_array($ttokens)) {
							$this->log->write("notice", "[A2] " . count($ttokens) . " trusted in database");
							if(in_array($Btoken, $ttokens)) {
								$this->log->write("notice", "[A2] " . $Btoken . " is already trusted");
								print "Already trusted";
								exit();
							}
						} else {
							$this->log->write("error", "[A2] " . $Btoken . " is no knower and no trusted. Aborting... It will need to repeat all process");
						}
					}
				} else {
					$this->log->write("error", "[A2] Time is expired. Deadline was " . $this->rsa->get_time_limit($this->rsa->simple_decrypt($key[2]), true) . " (now is " . date("Y-m-d H:i:s") . ")");
					$this->rsa->get_time_error();
				}
			} else {
				$this->log->write("error", "[A2] Time is expired. Deadline was " . $this->rsa->get_time_limit($this->rsa->simple_decrypt($key[2]), true) . " (now is " . date("Y-m-d H:i:s") . ")");
				$this->rsa->get_time_error();
			}
		} else {
			$this->log->write("error", "[A2] Can't explode 'reconfirm_friend' request. Probably time expired for A2...");
			$this->rsa->get_time_error();
		}
		$this->log->close();
	}
}
?>