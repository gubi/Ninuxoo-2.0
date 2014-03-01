<?php
/**
* Ninuxoo 2.0
*
* PHP Version 5.3
*
* @copyright 2013-2014 Alessandro Gubitosi / Gubi (http://iod.io)
* @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3
* @link https://github.com/gubi/Ninuxoo-2.0
*/

/**
* A class for manage device RSA keys
*
* _
*
* @package	Ninuxoo 2.0
* @author		Alessandro Gubitosi <gubi.ale@iod.io>
* @license 	http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3
* @access		public
* @link		https://github.com/gubi/Ninuxoo-2.0/blob/master/common/include/classes/rsa.class.php
* @todo		Create __construct function and clean all $dir params in functions
*/
class rsa {
	/**
	* Generate AES key
	*
	* @param string $dir Temp dir to save the key file
	* @return void
	* @access  private
	*/
	private function gen_aes_key($dir) {
		return shell_exec('openssl rand -base64 32 -out ' . $dir . 'aesKey~');
	}
	/**
	* Remove AES key
	*
	* @param string $dir Temp dir where the temp key file is placed
	* @return void
	* @access  private
	*/
	private function rm_aes_key($dir) {
		return shell_exec('rm ' . $dir . 'aesKey~');
	}
	
	/**
	* Print time error and exit
	*
	* @return void
	* @access  public
	*/
	public function get_time_error() {
		print "Time expired";
		exit();
	}
	/**
	* Return time limit difference
	*
	* @param bool $limit Is limited time
	* @param bool $show_deadline Show the deadline
	* @return string $sec_diff Time difference in seconds
	* @access  public
	*/
	public function get_time_limit($limit = false, $show_deadline = false) {
		date_default_timezone_set("Europe/Rome");
		
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
			$sec_diff = ((strtotime($data) - strtotime(date("Y-m-d H:i:s"))) > 0) ? true : false;
		} else {
			$sec_diff = date('Y-m-d H:i:s', strtotime($to_string, strtotime($data)));
		}
		
		return $sec_diff;
	}
	/**
	* Extract token from given RSA public key
	*
	* RSA 2048 structure taken from: http://www.php.net/manual/es/function.openssl-pkey-get-public.php#104439
	*
	* @param string $key RSA public key
	* @return string $token The token
	* @access  public
	*/
	public function get_token($key) {
		$token = md5(strstr(str_replace(array("MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA", "\n"), "", str_replace(array("-----BEGIN PUBLIC KEY-----", "-----END PUBLIC KEY-----"), "", $key)), "ID", true));
		return $token;
	}
	/**
	* Return NAS owner token
	*
	* @see rsa::get_token() Get_token
	* @return string $owner_token The owner token
	* @access  public
	*/
	public function my_token() {
		$config = parse_ini_file(str_replace("classes", "conf", __DIR__) . "/config.ini", 1);
		$owner_token = $this->get_token($config["NAS"]["root_dir"]);
		return $owner_token;
	}
	
	/**
	* Simple encrypt string
	*
	* Thanks to http://stackoverflow.com/a/1289114 :)
	*
	* @see rsa::simple_decrypt() Simple_decrypt
	* @param string $text The string to encrypt
	* @param bool $mcrypt Random encryption
	* @return string $encrypted The encrypted string
	* @access  public
	*/
	public function simple_encrypt($text, $mcrypt = false) {
		ini_set("max_execution_time", 3000);
		if(!$mcrypt) {
			$encrypted = trim(shell_exec('echo "' . $text . '" | openssl enc -base64'));
		} else {
			$encrypted = trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, SALT, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
		}
		return $encrypted;
	}
	/**
	* Simple decrypt string
	*
	* Thanks to http://stackoverflow.com/a/1289114 :)
	*
	* @see rsa::simple_encrypt() Simple_encrypt
	* @param string $text The simple encrypted string
	* @param bool $mcrypt Random decrypted
	* @return string $decrypted The decrypted string
	* @access  public
	*/
	public function simple_decrypt($text, $mcrypt = false) {
		ini_set("max_execution_time", 3000);
		if(!$mcrypt) {
			$decrypted = trim(shell_exec('echo "' . $text . '" | openssl enc -base64 -d'));
		} else {
			$decrypted = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, SALT, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
		}
		return $decrypted;
	}
	
	/**
	* Simple encrypt string with device private RSA key
	*
	* @see rsa::simple_private_decrypt() Simple_private_decrypt
	* @param string $string The string to encrypt
	* @return string $encrypted The encrypted string
	* @access  public
	*/
	public function simple_private_encrypt($string) {
		ini_set("max_execution_time", 3000);
		shell_exec("echo '" . $string . "' | openssl rsautl -encrypt -inkey " . str_replace("classes", "conf", __DIR__) . "/rsa_2048_priv.pem > " . str_replace("classes", "conf", __DIR__) . "/message.encrypted");
		$encrypted = shell_exec('cat ' . str_replace("classes", "conf", __DIR__) . '/message.encrypted | base64 - && rm ' . str_replace("classes", "conf", __DIR__) . '/message.encrypted');
		return $encrypted;
	}
	/**
	* Simple decrypt string with device private RSA key
	*
	* @see rsa::simple_private_encrypt() Simple_private_encrypt
	* @param string $string The string to encrypt
	* @return string $decrypted The encrypted string
	* @access  public
	*/
	public function simple_private_decrypt($string) {
		shell_exec('echo "' . $string . '" | base64 -d - > ' . str_replace("classes", "conf", __DIR__) . '/message.encrypted');
		shell_exec("cat " . str_replace("classes", "conf", __DIR__) . "/message.encrypted | openssl rsautl -decrypt -inkey " . str_replace("classes", "conf", __DIR__) . "/rsa_2048_priv.pem > " . str_replace("classes", "conf", __DIR__) . "/message.decrypted && rm " . str_replace("classes", "conf", __DIR__) . "/message.encrypted");
		$decrypted = shell_exec('cat ' . str_replace("classes", "conf", __DIR__) . '/message.decrypted && rm ' . str_replace("classes", "conf", __DIR__) . '/message.decrypted');
		return $decrypted;
	}
	
	/**
	* Encrypt RSA key with device public RSA key
	*
	* @see rsa::private_decrypt() Private_decrypt
	* @see link_nas::first_response() Link_nas -> first_response()
	* @see link_nas::second_response() Link_nas -> second_response()
	* @param string $dir Directory where device RSA key is placed
	* @param string $pub_key File name of device public RSA key
	* @param string $key_to_encrypt The RSA key that must be encrypted
	* @param null|bool $time_limit Append the time limit to the output
	* @return string $public_encrypted The encrypted key served for link_nas() class
	* @access  public
	*/
	public function public_encrypt($dir, $pub_key, $key_to_encrypt, $time_limit = null){
		$this->gen_aes_key($dir);
		shell_exec('openssl enc -aes-256-cbc -salt -in ' . $dir . $key_to_encrypt . ' -out ' . $dir . 'rsa_2048_pub.enc~ -pass file:' . $dir . 'aesKey~');
		shell_exec('openssl rsautl -encrypt -inkey ' . $dir . $pub_key . ' -pubin -in ' . $dir . 'aesKey~ -out ' . $dir . 'aesKey.enc~');
		$encrypted = shell_exec('cat ' . $dir . '/rsa_2048_pub.enc~ | base64 -');
		$enckey = shell_exec('cat ' . $dir . 'aesKey.enc~ | base64 -');
		shell_exec('rm ' . $dir . '/rsa_2048_pub.enc~ ' . $dir . 'aesKey.enc~');
		$this->rm_aes_key($dir);
		
		$public_encrypted = trim($encrypted) . "::::" . trim($enckey) . (($time_limit !== null && $this->get_time_limit($time_limit, true)) ? "::::" . $this->simple_encrypt($time_limit) : "");
		return $public_encrypted;
	}
	/**
	* Decrypt RSA key with device private RSA key
	*
	* @see rsa::private_decrypt() Private_decrypt
	* @see link_nas::second_response() Link_nas -> second_response()
	* @see link_nas::third_response() Link_nas -> third_response()
	* @param string $dir Directory where device RSA key is placed
	* @param string $priv_key File name of device private RSA key
	* @param string $crypted_aes _
	* @param string $crypted_key Encrypted RSA key to decrypt
	* @return string $public_key Decrypted public RSA key
	* @access  public
	*/
	public function private_decrypt($dir, $priv_key, $crypted_aes, $crypted_key){
		shell_exec('echo "' . $crypted_aes . '" | base64 -d - > ' . $dir . 'aesKey.enc~');
		shell_exec('echo "' . $crypted_key . '" | base64 -d - > ' . $dir . 'rsa_2048_pub.enc~');
		shell_exec('openssl rsautl -decrypt -inkey ' . $dir . $priv_key . ' -in ' . $dir . 'aesKey.enc~ -out ' . $dir . 'aesKey.decrypted');
		shell_exec('openssl enc -d -aes-256-cbc -in ' . $dir . 'rsa_2048_pub.enc~ -out ' . $dir . 'rsa_2048_pub.dec~ -pass file:' . $dir . 'aesKey.decrypted');
		$public_key = shell_exec('cat ' . $dir . '/rsa_2048_pub.dec~'); 
		shell_exec('rm ' . $dir . '/aesKey.decrypted ' . $dir . '/aesKey.enc~ ' . $dir . 'rsa_2048_pub.enc~ ' . $dir . 'rsa_2048_pub.dec~');
		
		return $public_key;
	}
}
?>