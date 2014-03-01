<?php
/**
* Ninuxoo 2.0
*
* PHP Version 5.3
*
* @copyright 2013 Alessandro Gubitosi / Gubi (http://iod.io)
* @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3
* @link https://github.com/gubi/Ninuxoo-2.0
*/

/**
* A class for send email where the program sendmail is not installed
*
* 
*
* @package	Ninuxoo 2.0
* @author		Alessandro Gubitosi <gubi.ale@iod.io>
* @license 	http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3
* @access		public
* @link		https://github.com/gubi/Ninuxoo-2.0/blob/master/common/include/classes/sendmail.class.php
*/
class Sendmail {
	/**
	* Construct
	*
	* Initialize the class
	*
	* @global string $this->class_dir Current class directory
	* @global string $this->lib_dir Lib directory, based on $this->class_dir
	* @global string $this->dir Conf directory, based on $this->class_dir
	* @global string $this->config Parsed config.ini, based on $this->dir
	* @subpackage PEAR/Mail.php
	* @return void
	*/
	function __construct() {
		$this->class_dir = __DIR__;
		$this->lib_dir = str_replace("classes", "lib", $this->class_dir);
		$this->dir = str_replace("classes", "conf", $this->class_dir);
		$this->config = parse_ini_file($this->dir . "/config.ini", true);
	}
	
	/**
	* Get user preferred program
	*
	* @access private
	* @see link_nas::__construct() Construct
	* @return string $this->config["Mail"]["mail_program"] User preferred program
	*/
	private function get_program() {
		return $this->config["Mail"]["mail_program"];
	}
	
	/**
	* Set defaults mail headers
	*
	* @access private
	* @return array|string $headers|$headers_txt Mail headers
	*/
	private function get_program() {
		$headers_txt = "";
		
		$headers["Content-Type"] = "text/plain; charset=UTF-8";
		$headers["Content-Transfer-Encoding"] = "quoted-printable";
		$headers["X-Mailer"] = "PHP5";
		$headers["MIME-Version"] = "1.0";
		$headers["Organization"] = "Ninux.org";
		$headers["From"] = "Ninuxoo <ninuxoo@ninux.org>";
		$headers["Reply-to"] = "Ninux.org <contatti@ninux.org>";
		
		if($this->get_program() == "server") {
			foreach ($headers as $k => $v) {
				$headers_txt .= $k . ": " . $v . "\r\n";
			}
			return $headers_txt;
		} else {
			return $headers;
		}
	}
	
	/**
	* Send mail
	*
	* @param string $to Recipient of the email
	* @param string $subject Subject of the email
	* @param string $body Body of the email
	* @access public
	* @see link_nas::__construct() Construct
	* @see link_nas::get_program() get_program
	* @uses PEAR/Mail.php PEAR/Mail.php
	* @return bool|array Mail send status
	*/
	public function send($to, $subject, $body) {
		$body .= "\n\nUn saluto\n--=20\nNinuxoo\nThe Ninux.org Community Network";
		
		if($this->get_program() == "program") {
			require_once($this->lib_dir . "/PEAR/Mail.php");
			
			$headers = $this->headers();
			$headers["Subject"] = $subject;
			
			$params["sendmail_path"] = $this->config["Mail"]["sendmail_path"];
			$params["host"] = $this->config["Mail"]["host"];
			$params["port"] = $this->config["Mail"]["port"];
			$params["auth"] = $this->config["Mail"]["auth"];
			$params["username"] = $this->config["Mail"]["username"];
			$params["password"] = $this->config["Mail"]["password"];
			//print_r($headers);
			//print_r($params);
			$mail_object =& Mail::factory("sendmail", $params);
			$mail = $mail_object->send($to, $headers, $body);
			if (PEAR::isError($mail)) {
				return $mail->getMessage();
			} else {
				return $mail;
			}
		} else {
			return mail($to, $subject, wordwrap($body, 120, "\n"), $this->headers());
		}
	}
}
?>
