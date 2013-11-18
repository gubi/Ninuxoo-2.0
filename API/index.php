<?php
header("Content-type: text/plain;");
class rsa {
	private function gen_aes_key($dir) {
		return shell_exec('openssl rand -base64 32 -out ' . $dir . 'aesKey~');
	}
	private function rm_aes_key($dir) {
		return shell_exec('rm ' . $dir . 'aesKey~');
	}
	
	public function get_time_error() {
		print "Time expired";
		exit();
	}
	public function get_time_limit($limit = false, $show_deadline = false) {
		if(!isset($limit) || $limit == false || $limit == null) {
			$data = date("Y-m-d H:i:s");
		} else {
			$data = $limit;
		}
		
		list($date, $time) = explode(" ", $data);
		list($year, $month, $day) = explode("-", $date);
		list($hour, $minute, $second) = explode(":", $time);
		
		if(!isset($limit) || $limit == false || $limit == null) {
			$deadline = mktime($hour+1, $minute, $second, $month, $day, $year);
			$to_string = "+1 hour";
		} else {
			$deadline = mktime($hour, $minute+1, $second, $month, $day, $year);
			$to_string = "+3 minute";
		}
		$now = time();
		$difference = $deadline - $now;
		
		if(!$show_deadline) {
			$sec_diff = (intval($difference - ($hours_left * (60 * 60)) - ($mins_left * 60)) > 0) ? true : false;
		} else {
			$sec_diff = date('Y-m-d H:i:s', strtotime($to_string, strtotime($data)));
		}
		
		return $sec_diff;
	}
	public function get_token($key) {
		/*
		SSL KEY STRUCTURE:
		______________________________________________________________
		
			-----BEGIN PUBLIC KEY-----
			X509 signature + PEM sig + modulus + 'ID' + exponent
			-----END PUBLIC KEY----- 
		______________________________________________________________
		*/
		return md5(strstr(str_replace(array("MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA", "\n"), "", str_replace(array("-----BEGIN PUBLIC KEY-----", "-----END PUBLIC KEY-----"), "", $key)), "ID", true));
	}
	/**
	// Simple encrypt and decrypt string.
	// Thanks to http://stackoverflow.com/a/1289114 :)
	*/
	public function simple_encrypt($text) {
		return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, SALT, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
	}
	public function simple_decrypt($text) {
		return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, SALT, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
	}
	public function public_encrypt($dir, $pub_key, $key_to_encrypt, $time_limit = null){
		if($this->get_time_limit($time_limit, true)) {
			$this->gen_aes_key($dir);
			shell_exec('openssl enc -aes-256-cbc -salt -in ' . $dir . $key_to_encrypt . ' -out ' . $dir . 'rsa_2048_pub.enc~ -pass file:' . $dir . 'aesKey~');
			shell_exec('openssl rsautl -encrypt -inkey ' . $dir . $pub_key . ' -pubin -in ' . $dir . 'aesKey~ -out ' . $dir . 'aesKey.enc~');
			$encrypted = shell_exec('cat ' . $dir . '/rsa_2048_pub.enc~ | base64 -');
			$enckey = shell_exec('cat ' . $dir . 'aesKey.enc~ | base64 -');
			shell_exec('rm ' . $dir . '/rsa_2048_pub.enc~ ' . $dir . 'aesKey.enc~');
			$this->rm_aes_key($dir);
			
			return trim($encrypted) . "::::" . trim($enckey) . "::::" . $this->simple_encrypt($time_limit);
		} else {
			return false;
		}
	}
	public function private_decrypt($dir, $priv_key, $crypted_aes, $crypted_key, $time_limit = null){
		if($this->get_time_limit($time_limit, true)) {
			shell_exec('echo "' . $crypted_aes . '" | base64 -d - > ' . $dir . 'aesKey.enc~');
			shell_exec('echo "' . $crypted_key . '" | base64 -d - > ' . $dir . 'rsa_2048_pub.enc~');
			shell_exec('openssl rsautl -decrypt -inkey ' . $dir . $priv_key . ' -in ' . $dir . 'aesKey.enc~ -out ' . $dir . 'aesKey.decrypted');
			shell_exec('openssl enc -d -aes-256-cbc -in ' . $dir . 'rsa_2048_pub.enc~ -out ' . $dir . 'rsa_2048_pub.dec~ -pass file:' . $dir . 'aesKey.decrypted');
			$public_key = shell_exec('cat ' . $dir . '/rsa_2048_pub.dec~'); 
			shell_exec('rm ' . $dir . '/aesKey.decrypted ' . $dir . '/aesKey.enc~ ' . $dir . 'rsa_2048_pub.enc~ ' . $dir . 'rsa_2048_pub.dec~');
			
			return $public_key;
		} else {
			return false;
		}
	}
}

$trusted_dir = "../common/include/conf/trusted/";
$main_dir = "../common/include/conf/";

if(isset($_GET["token"]) && trim($_GET["token"]) !== "") {
	foreach(glob($trusted_dir . "*.pem") as $filename) {
		$tokens[] = str_replace(array($trusted_dir, ".pem"), "", $filename);
	}
	if(in_array($_GET["token"], $tokens)) {
		if(isset($_GET["search"]) && trim($_GET["search"]) !== "") {
			$search = shell_exec('grep -i "' . urldecode($_GET["search"]) . '" listing');
			$results = array_filter(explode("\n", $search));
			
			if(count($results) > 0) {
				print_r($results);
			} else {
				"No results";
			}
		} else {
			print "Nothing to do.";
		}
	}
} else {
	require_once("../common/include/funcs/_blowfish.php");
	
	$rsa = new rsa();
	
	if(isset($_GET["make_friend"]) && trim($_GET["make_friend"]) == "true") {
		if(isset($_GET["debug"])) {
			print "-----\n";
			print "| B1 |\n";
			print "-----\n\n";
		}
		
		// B request
		$fb = fopen($main_dir . "rsa_2048_pub.pem", "r");
		$Bkey = fread($fb, 8192);
		fclose($fb);
		
		$limit = (isset($_GET["debug"]) ? false : date("Y-m-d H:i:s"));
		$time_limit = $rsa->get_time_limit($limit);
		
		$friendship_request = PMA_blowfish_encrypt(trim($Bkey), $rsa->get_time_limit($limit, true));
		$Brequest = PMA_blowfish_encrypt($friendship_request . "::::" . $rsa->get_time_limit($limit, true), "Shoot some hoops?");
		
		if(isset($_GET["debug"])) {
			print $rsa->get_time_limit($limit, true) . "\n\n";
			print "B request:\n" . rawurlencode($Brequest);
		} else {
			print rawurlencode($Brequest);
		}
	} else if(isset($_GET["friend_request"]) && trim($_GET["friend_request"]) !== "") {
		if(isset($_GET["debug"])) {
			print "-----\n";
			print "| A1 |\n";
			print "-----\n\n";
		}
		
		// A parse
		$friendship_request = explode("::::", PMA_blowfish_decrypt(rawurldecode($_GET["friend_request"]), "Shoot some hoops?"));
		$time_limit = $friendship_request[1];
		
		if($rsa->get_time_limit($time_limit)) {
			$resp = PMA_blowfish_decrypt($friendship_request[0], $time_limit);
			$Bkey = $resp;
		
			// A response
			$fb = fopen($main_dir . "rsa_2048_pub.pem", "r");
			$Akey = fread($fb, 8192);
			fclose($fb);
			
			$Atoken = $rsa->get_token($Akey);
			$Btoken = $rsa->get_token($Bkey);
			
			$knower = fopen($trusted_dir . $Btoken . ".pem~", "wb");
			fwrite($knower, $Bkey);
			fclose($knower);
			
			$encrypted_A_response = $rsa->public_encrypt($main_dir, "trusted/"  . $Atoken . ".pem~", "rsa_2048_pub.pem", $time_limit);
			if($encrypted_A_response) {
				if(isset($_GET["debug"])) {
					print "B key:\n" . $Bkey;
					print "\n\nB token: " . $Btoken . "\n\n";
					print "Encrypted A response:\n" . $encrypted_A_response . "\n\n";
					print "Urlencoded A response:\n" . rawurlencode($encrypted_A_response) . "\n\n";
				} else {
					print rawurlencode($encrypted_A_response);
				}
			} else {
				$rsa->get_time_error();
			}
		} else {
			$rsa->get_time_error();
		}
	} else if(isset($_GET["confirm_friend"]) && trim($_GET["confirm_friend"]) !== "") {
		if(isset($_GET["debug"])) {
			print "-----\n";
			print "| B2 |\n";
			print "-----\n\n";
		}
		
		// B parse
		$key = explode("::::", rawurldecode($_GET["confirm_friend"]));
		if(count($key) > 1) {
			if($rsa->get_time_limit($rsa->simple_decrypt($key[2]))) {
				$Akey = $rsa->private_decrypt($main_dir, "rsa_2048_priv.pem", $key[1], $key[0], $rsa->simple_decrypt($key[2]));
				if($Akey) {
					$Atoken = $rsa->get_token($Akey);
					$knower = fopen($trusted_dir . $Atoken . ".pem", "wb");
					fwrite($knower, $Akey);
					fclose($knower);
					
					$encrypted_B_response = $rsa->public_encrypt($main_dir, "trusted/"  . $Atoken . ".pem", "rsa_2048_pub.pem", $rsa->simple_decrypt($key[2]));
					if($encrypted_B_response) {
						if(isset($_GET["debug"])) {
							print "Decrypted A key is:\n" . $Akey . "\n\n";
							print "A is trusted and its token is: " . $Atoken . "\n\n";
							print "Encrypted B response:\n" . rawurlencode($encrypted_B_response);
						} else {
							print rawurlencode($encrypted_B_response);
						}
					} else {
						$rsa->get_time_error();
					}
				} else {
					$rsa->get_time_error();
				}
			} else {
				$rsa->get_time_error();
			}
		} else {
			$rsa->get_time_error();
		}
	} else if(isset($_GET["reconfirm_friend"]) && trim($_GET["reconfirm_friend"]) !== "") {
		if(isset($_GET["debug"])) {
			print "-----\n";
			print "| A2 |\n";
			print "-----\n\n";
		}
		
		// A 2nd parse
		$key = explode("::::", rawurldecode($_GET["reconfirm_friend"]));
		if(count($key) > 1) {
			if($rsa->get_time_limit($rsa->simple_decrypt($key[2]))) {
				$Bkey = $rsa->private_decrypt($main_dir, "rsa_2048_priv.pem", $key[1], $key[0], $rsa->simple_decrypt($key[2]));
				if($Bkey) {
					$Btoken = $rsa->get_token($Bkey);
					if(isset($_GET["debug"])) {
						print "B is trusted and its token is: " . $Btoken;
					} else {
						print "Hello " . $Btoken . ", nice to meet you!";
					}
					// Check knowers
					foreach(glob($trusted_dir . "*.pem~") as $filename) {
						$tokens[] = str_replace(array($trusted_dir, ".pem~"), "", $filename);
					}
					if(is_array($tokens)) {
						if(in_array($Btoken, $tokens)) {
							// Check_keys
							$fb = fopen($trusted_dir . $Btoken . ".pem~", "r");
							$knowerBkey = fread($fb, 8192);
							fclose($fb);
							// Rename knower to trusted
							if(trim($knowerBkey) == trim($Bkey)) {
								rename($trusted_dir . $Btoken . ".pem~", $trusted_dir . $Btoken . ".pem");
							}
						}
					}
				} else {
					$rsa->get_time_error();
				}
			} else {
				$rsa->get_time_error();
			}
		} else {
			$rsa->get_time_error();
		}
	} else {
		print "You must to provide token!";
	}
}
?>