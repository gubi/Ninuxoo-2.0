<?php
header("Content-type: text/plain;");
require_once("../common/include/classes/logging.class.php");

$log = new Logging();
$log->file("../common/include/log/ninuxoo.log");

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
	require_once("../common/include/classes/rsa.class.php");
	require_once("../common/include/funcs/_blowfish.php");
	
	$rsa = new rsa();
	$trusted_dir = "../common/include/conf/trusted/";
	$main_dir = "../common/include/conf/";
	
	if(isset($_GET["make_friend"]) && trim($_GET["make_friend"]) == "true") {
		// B1 request
		$fb = fopen($main_dir . "rsa_2048_pub.pem", "r");
		$Bkey = fread($fb, 8192);
		fclose($fb);
		
		$limit = (isset($_GET["debug"]) ? false : date("Y-m-d H:i:s"));
		$time_limit = $rsa->get_time_limit($limit);
		
		$log->write("notice", "[B1] Started with time limit of " . $time_limit . " seconds");
		
		$friendship_request = PMA_blowfish_encrypt(trim($Bkey), $rsa->get_time_limit($limit, true));
		$Brequest = PMA_blowfish_encrypt($friendship_request . "::::" . $rsa->get_time_limit($limit, true), "Shoot some hoops?");
		
		$log->write("notice", "[B1] Time limit is fixed at " . $rsa->get_time_limit($limit, true));
		$log->write("notice", "[B1] Request is:\n" . rawurlencode($Brequest));
		
		print rawurlencode($Brequest);
		$log->close();
	} else if(isset($_GET["friend_request"]) && trim($_GET["friend_request"]) !== "") {
		// A1 parse
		$friendship_request = explode("::::", PMA_blowfish_decrypt(rawurldecode($_GET["friend_request"]), "Shoot some hoops?"));
		$time_limit = $friendship_request[1];
		
		if($rsa->get_time_limit($time_limit)) {
			$log->write("notice", "[A1] Started with deadline " . $rsa->get_time_limit($time_limit, true));
			
			$resp = PMA_blowfish_decrypt($friendship_request[0], $time_limit);
			$Bkey = $resp;
			
			// A response
			$fb = fopen($main_dir . "rsa_2048_pub.pem", "r");
			$Akey = fread($fb, 8192);
			fclose($fb);
			
			$Atoken = $rsa->get_token($Akey);
			$Btoken = $rsa->get_token($Bkey);
			$log->write("notice", "[A1] Public key received with token " . $Btoken);
			
			$knower = fopen($trusted_dir . $Btoken . ".pem~", "wb");
			$log->write("notice", "[A1] NAS " . $Btoken . " added to '" . $trusted_dir . $Btoken . ".pem~' as KNOWER");
			fwrite($knower, $Bkey);
			fclose($knower);
			
			$encrypted_A_response = $rsa->public_encrypt($main_dir, "trusted/"  . $Atoken . ".pem~", "rsa_2048_pub.pem", $time_limit);
			if($encrypted_A_response) {
				$log->write("notice", "[A1] Response for B2 is:\n" . rawurlencode($encrypted_A_response));
				print rawurlencode($encrypted_A_response);
			} else {
				$log->write("error", "[A1] Time is expired. Deadline was " . $rsa->get_time_limit($time_limit, true) . " (now is " . date("Y-m-d H:i:s") . ")");
				$rsa->get_time_error();
			}
		} else {
			$log->write("error", "[A1] Time is expired. Deadline was " . $rsa->get_time_limit($time_limit, true) . " (now is " . date("Y-m-d H:i:s") . ")");
			$rsa->get_time_error();
		}
		$log->close();
	} else if(isset($_GET["confirm_friend"]) && trim($_GET["confirm_friend"]) !== "") {
		// B2 parse
		$key = explode("::::", rawurldecode($_GET["confirm_friend"]));
		if(count($key) > 1) {
			if($rsa->get_time_limit($rsa->simple_decrypt($key[2]))) {
				$Akey = $rsa->private_decrypt($main_dir, "rsa_2048_priv.pem", $key[1], $key[0], $rsa->simple_decrypt($key[2]));
				if($Akey) {
					$log->write("notice", "[B2] Started with deadline " . $rsa->get_time_limit($rsa->simple_decrypt($key[2]), true));
					$Atoken = $rsa->get_token($Akey);
					$log->write("notice", "[B2] Public key received with token " . $Atoken);
					$trusted = fopen($trusted_dir . $Atoken . ".pem", "wb");
					fwrite($trusted, $Akey);
					fclose($trusted);
					$log->write("notice", "[B2] NAS " . $Atoken . " added to '" . $trusted_dir . $Atoken . ".pem' as TRUSTED");
					
					$encrypted_B_response = $rsa->public_encrypt($main_dir, "trusted/"  . $Atoken . ".pem", "rsa_2048_pub.pem", $rsa->simple_decrypt($key[2]));
					if($encrypted_B_response) {
						$log->write("notice", "[B2] Response for A2 is:\n" . rawurlencode($encrypted_B_response));
						print rawurlencode($encrypted_B_response);
					} else {
						$log->write("error", "[B2] Time is expired. Deadline was " . $rsa->get_time_limit($rsa->simple_decrypt($key[2]), true) . " (now is " . date("Y-m-d H:i:s") . ")");
						$rsa->get_time_error();
					}
				} else {
					$log->write("error", "[B2] Time is expired. Deadline was " . $rsa->get_time_limit($rsa->simple_decrypt($key[2]), true) . " (now is " . date("Y-m-d H:i:s") . ")");
					$rsa->get_time_error();
				}
			} else {
				$log->write("error", "[B2] Time is expired. Deadline was " . $rsa->get_time_limit($rsa->simple_decrypt($key[2]), true) . " (now is " . date("Y-m-d H:i:s") . ")");
				$rsa->get_time_error();
			}
		} else {
			$log->write("error", "Can't explode 'confirm_friend' request. Probably time expired for B2...");
			$rsa->get_time_error();
		}
		$log->close();
	} else if(isset($_GET["reconfirm_friend"]) && trim($_GET["reconfirm_friend"]) !== "") {
		// A2 parse
		$key = explode("::::", rawurldecode($_GET["reconfirm_friend"]));
		if(count($key) > 1) {
			if($rsa->get_time_limit($rsa->simple_decrypt($key[2]))) {
				$Bkey = $rsa->private_decrypt($main_dir, "rsa_2048_priv.pem", $key[1], $key[0], $rsa->simple_decrypt($key[2]));
				if($Bkey) {
					$log->write("notice", "[A2] Started with deadline " . $rsa->get_time_limit($rsa->simple_decrypt($key[2]), true));
					$Btoken = $rsa->get_token($Bkey);
					$log->write("notice", "[A2] Public key received with token " . $Btoken);
					print "Hello " . $Btoken . ", nice to meet you!";
					
					// Check knowers
					foreach(glob($trusted_dir . "*.pem~") as $filename) {
						$ktokens[] = str_replace(array($trusted_dir, ".pem~"), "", $filename);
					}
					if(is_array($ktokens)) {
						$log->write("notice", "[A2] " . count($ktokens) . " knowers in database");
						if(in_array($Btoken, $ktokens)) {
							// Check_keys
							$fb = fopen($trusted_dir . $Btoken . ".pem~", "r");
							$knowerBkey = fread($fb, 8192);
							fclose($fb);
							// Rename knower to trusted
							if(trim($knowerBkey) == trim($Bkey)) {
								rename($trusted_dir . $Btoken . ".pem~", $trusted_dir . $Btoken . ".pem");
								$log->write("notice", "[A2] NAS " . $Btoken . " added to '" . $trusted_dir . $Btoken . ".pem' as TRUSTED");
							}
						}
					} else {
						$log->write("notice", "[A2] There's no knowers in the database.");
						
						// Check trusted
						foreach(glob($trusted_dir . "*.pem") as $filename) {
							$ttokens[] = str_replace(array($trusted_dir, ".pem"), "", $filename);
						}
						if(is_array($ttokens)) {
							$log->write("notice", "[A2] " . count($ttokens) . " trusted in database");
							if(in_array($Btoken, $ttokens)) {
								$log->write("notice", "[A2] " . $Btoken . " is already trusted");
							}
						} else {
							$log->write("error", "[A2] " . $Btoken . " is no knower and no trusted. Aborting... It will need to repeat all process");
						}
					}
				} else {
					$log->write("error", "[A2] Time is expired. Deadline was " . $rsa->get_time_limit($rsa->simple_decrypt($key[2]), true) . " (now is " . date("Y-m-d H:i:s") . ")");
					$rsa->get_time_error();
				}
			} else {
				$log->write("error", "[A2] Time is expired. Deadline was " . $rsa->get_time_limit($rsa->simple_decrypt($key[2]), true) . " (now is " . date("Y-m-d H:i:s") . ")");
				$rsa->get_time_error();
			}
		} else {
			$log->write("error", "[A2] Can't explode 'reconfirm_friend' request. Probably time expired for A2...");
			$rsa->get_time_error();
		}
		$log->close();
	} else {
		print "You must to provide token!";
		$log->write("notice", "Rejected request from " . $_SERVER["REMOTE_ADDR"] . " without token");
		$log->close();
	}
}
?>