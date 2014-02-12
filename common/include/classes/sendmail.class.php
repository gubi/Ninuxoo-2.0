<?php

class Sendmail {
	function __construct() {
		$this->class_dir = __DIR__;
		$this->lib_dir = str_replace("classes", "lib", $this->class_dir);
		$this->dir = str_replace("classes", "conf", $this->class_dir);
		$this->config = parse_ini_file($this->dir . "/config.ini", true);
	}
	private function get_program() {
		return $this->config["Mail"]["mail_program"];
	}
	private function headers() {
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
