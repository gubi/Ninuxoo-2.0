<?php

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
		RSA 2048 structure taken from: http://www.php.net/manual/es/function.openssl-pkey-get-public.php#104439
		*/
		return md5(strstr(str_replace(array("MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA", "\n"), "", str_replace(array("-----BEGIN PUBLIC KEY-----", "-----END PUBLIC KEY-----"), "", $key)), "ID", true));
	}
	public function my_token() {
		$config = parse_ini_file(str_replace("classes", "conf", __DIR__) . "/config.ini", 1);
		return $this->get_token($config["NAS"]["root_dir"]);
	}
	/**
	// Simple encrypt and decrypt string.
	// Thanks to http://stackoverflow.com/a/1289114 :)
	*/
	public function simple_encrypt($text) {
		ini_set("max_execution_time", 3000);
		return trim(shell_exec('echo "' . $text . '" | openssl enc -base64'));
		//return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, SALT, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
	}
	public function simple_decrypt($text) {
		return trim(shell_exec('echo "' . $text . '" | openssl enc -base64 -d'));
		//return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, SALT, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
	}
	public function simple_private_encrypt($string) {
		ini_set("max_execution_time", 3000);
		shell_exec("echo '" . $string . "' | openssl rsautl -encrypt -inkey " . str_replace("classes", "conf", __DIR__) . "/rsa_2048_priv.pem > " . str_replace("classes", "conf", __DIR__) . "/message.encrypted");
		$encrypted = shell_exec('cat ' . str_replace("classes", "conf", __DIR__) . '/message.encrypted | base64 - && rm ' . str_replace("classes", "conf", __DIR__) . '/message.encrypted');
		return $encrypted;
	}
	public function simple_private_decrypt($string) {
		shell_exec('echo "' . $string . '" | base64 -d - > ' . str_replace("classes", "conf", __DIR__) . '/message.encrypted');
		shell_exec("cat " . str_replace("classes", "conf", __DIR__) . "/message.encrypted | openssl rsautl -decrypt -inkey " . str_replace("classes", "conf", __DIR__) . "/rsa_2048_priv.pem > " . str_replace("classes", "conf", __DIR__) . "/message.decrypted && rm " . str_replace("classes", "conf", __DIR__) . "/message.encrypted");
		$decrypted = shell_exec('cat ' . str_replace("classes", "conf", __DIR__) . '/message.decrypted && rm ' . str_replace("classes", "conf", __DIR__) . '/message.decrypted');
		return $decrypted;
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
?>