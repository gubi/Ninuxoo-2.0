<?php
header("Content-type: text/plain;");
function get_token($key) {
	return md5(strstr(str_replace(array("MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA", "\n"), "", str_replace(array("-----BEGIN PUBLIC KEY-----", "-----END PUBLIC KEY-----"), "", $key)), "ID", true));
}

//print md5(md5_file("../common/include/conf/trusted/gubi.pem") . date("Y-m-d") . microtime()) . date("Y-m-d H:i:s") . microtime();

if(isset($_GET["token"]) && trim($_GET["token"]) !== "") {
	$trusted_dir = "../common/include/conf/trusted/";
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
	/*
	SSL key structure:
	--------------------------------------------------------------------------------------------
	-----BEGIN PUBLIC KEY-----
	X509 signature + PEM sig + modulus + 'ID' + exponent
	-----END PUBLIC KEY----- 
	--------------------------------------------------------------------------------------------
	*/
	$trusted_dir = "../common/include/conf/trusted/";
	$main_dir = "../common/include/conf/";
	if(isset($_GET["debug"])) {
		$password = date("Y-m-d_H");
	} else {
		$password = date("Y-m-d_H:i");
	}
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
		
		$Brequest = PMA_blowfish_encrypt($Bkey . "::::" . md5($password), $password);
		if(isset($_GET["debug"])) {
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
		
		$fa = fopen($main_dir . "rsa_2048_pub.pem", "r");
		$Akey = fread($fa, 8192);
		fclose($fa);
		
		// A parse
		$resp = explode("::::", PMA_blowfish_decrypt(rawurldecode($_GET["friend_request"]), $password));
		$Bkey = $resp[0];
		$md5_pass = $resp[1];
		if($md5_pass == md5($password)) {
			// A response
			$Btoken = get_token($Bkey);
			$password = sha1($Bkey);
			$encrypted_A_response = rawurlencode(PMA_blowfish_encrypt($Akey . "::::" . $Btoken, $password));
			if(isset($_GET["debug"])) {
				print "B key:\n" . $Bkey;
				print "\n\nB token: " . $Btoken;
				print "\nA key:\n" . $Akey . "\n\n";
				print "Encrypted A response:\n" . $encrypted_A_response;
			} else {
				print $encrypted_A_response;
			}
			
			$knower = fopen($trusted_dir . $Btoken . ".pem~", "wb");
			fwrite($knower, $_SERVER["REMOTE_ADDR"] . "\n\n" . $Bkey);
			fclose($knower);
		} else {
			print "Time expired";
		}
	} else if(isset($_GET["confirm_friend"]) && trim($_GET["confirm_friend"]) !== "") {
		if(isset($_GET["debug"])) {
			print "-----\n";
			print "| B2 |\n";
			print "-----\n\n";
		}
		
		$fb = fopen($main_dir . "rsa_2048_pub.pem", "r");
		$Bkey = fread($fb, 8192);
		fclose($fb);
		$password = sha1($Bkey);
		
		// B parse
		$resp = explode("::::", PMA_blowfish_decrypt(rawurldecode($_GET["confirm_friend"]), $password));
		$Akey = $resp[0];
		$Btoken = $resp[1];
		$Atoken = get_token($Akey);
		$password = sha1($Akey);
		
			// B response
			$encrypted_B_response = rawurlencode(PMA_blowfish_encrypt($Btoken, $password));
			
			if(isset($_GET["debug"])) {
				print "B token is: " . $Btoken . "\n";
				print "Decrypted A key is:\n" . $Akey . "\n\n";
				print "A is trusted and its token is: " . $Atoken . "\n\n";
				print "Encrypted A response:\n" . $encrypted_B_response;
			} else {
				print $encrypted_B_response;
			}
			$knower = fopen($trusted_dir . $Atoken . ".pem", "wb");
			fwrite($knower, $_SERVER["REMOTE_ADDR"] . "\n\n" . $Akey);
			fclose($knower);
		
	} else if(isset($_GET["reconfirm_friend"]) && trim($_GET["reconfirm_friend"]) !== "") {
		if(isset($_GET["debug"])) {
			print "-----\n";
			print "| A2 |\n";
			print "-----\n\n";
		}
		
		$fa = fopen($main_dir . "rsa_2048_pub.pem", "r");
		$Akey = fread($fa, 8192);
		fclose($fa);
		$password = sha1($Akey);
		
		// A 2nd parse
		$resp = explode("::::", PMA_blowfish_decrypt(rawurldecode($_GET["reconfirm_friend"]), $password));
		$Btoken = $resp[0];
		$md5_pass = $resp[1];
		
			if(isset($_GET["debug"])) {
				print "B is trusted and its token is: " . $Btoken;
			} else {
				print $Btoken;
			}
			foreach(glob($trusted_dir . "*.pem~") as $filename) {
				$tokens[] = str_replace(array($trusted_dir, ".pem~"), "", $filename);
			}
			if(in_array($Btoken, $tokens)) {
				rename($trusted_dir . $Btoken . ".pem~", $trusted_dir . $Btoken . ".pem");
			}
	} else {
		print "You must to provide token!";
	}
}
?>