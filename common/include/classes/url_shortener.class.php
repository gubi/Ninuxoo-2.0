<?php
//For more info about Yourls' API visit http://yourls.org/#API
class yourls {
	function __construct() {
		$this->yourls_url = "http://nnx.me/yourls-api.php";
		$this->yourls_token = "KcMk8hjh90y3U8fE3r2ZDl9lzYhRlp5YgnLgy0vVBFVKXrGeDAj1v8x0USay8vYsIMAojjwqrP1Xjx/dgKTAJw==";
		
		$this->obj = new stdClass();
		$this->obj->signature = $this->dec();
	}
	protected function dec() {
		$t = base64_decode($this->yourls_token);
		$k = "shorten";
		$iv = substr($t, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC));
		$dec = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, hash("sha256", $k, true), substr($t, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CBC)), MCRYPT_MODE_CBC, $iv), "\0");
		return $dec;
	}
	private function error($message = "Si e' verificato un errore") {
		print $message;
		exit();
	}
	
	public function format($format = "") {
		if(strlen(trim($format)) == 0) {
			$format = "simple"; // "jsonp", "json", "xml" or "simple"
		}
		$this->obj->format = $format;
	}
	public function action($action = "") {
		if(strlen(trim($action)) == 0) {
			$action = "shorturl"; // "expand", "url-stats", "stats", "db-stats" or "shorturl"
		}
		$this->obj->action = $action;
	}
	public function keyword($keyword = "") {
		if(strlen(trim($keyword)) == 0) {
			$keyword = "";
		}
		$this->obj->keyword = $keyword;
	}
	public function title($title = "") {
		if(strlen(trim($title)) == 0) {
			$title = "Ninuxoo page";
		}
		$this->obj->title = $title;
	}
	public function url($url) {
		if(strlen(trim($url)) > 0) {
			$this->obj->action = $url;
		} else {
			$this->error("Nessun URL da raccorciare");
		}
	}
	public function shorten($url = "") {
		if(strlen(trim($this->obj->format)) == 0) {
			$this->format();
		}
		if(strlen(trim($this->obj->action)) == 0) {
			$this->action();
		}
		if(strlen(trim($this->obj->keyword)) == 0) {
			$this->keyword();
		}
		if(strlen(trim($this->obj->title)) == 0) {
			$this->title();
		}
		if(strlen(trim($url)) == 0) {
			$this->error("Nessun URL da raccorciare");
		} else {
			$this->obj->url = trim($url);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->yourls_url);
			curl_setopt($ch, CURLOPT_HEADER, 0); // No header in the result
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Return, do not echo result
			curl_setopt($ch, CURLOPT_POST, 1); // This is a POST request
			curl_setopt($ch, CURLOPT_POSTFIELDS, array(// Data to POST
				"url" => $this->obj->url,
				"keyword" => $this->obj->keyword,
				"title" => $this->obj->title,
				"format" => $this->obj->format,
				"action" => $this->obj->action,
				"signature" => $this->obj->signature
			));

			// Fetch and return content
			$shorted = curl_exec($ch);
			curl_close($ch);
			//$shorted = file_get_contents($this->yourls_url . http_build_query($this->obj));
			return $shorted;
		}
	}
}
?>