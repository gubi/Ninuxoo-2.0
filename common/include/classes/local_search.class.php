<?php
if(!function_exists("json_encode")) {
	function json_encode($data) {
		$json = new Services_JSON();
		return $json->encode($data);
	}
}
if(!function_exists("json_decode")) {
	function json_decode($data) {
		$json = new Services_JSON();
		return $json->decode($data);
	}
}
	
class local_search {
	private $debug;
	private $response;
	private $resourcetry;
	private $params;
	
	public function __construct() {
		$this->class_dir = __DIR__;
		$this->dir = str_replace("classes", "conf", $this->class_dir);
		$this->conf = parse_ini_file($this->dir . "/config.ini", true);
		$this->root_dir = $this->conf["NAS"]["root_dir"];
		$this->root_share_dir = $this->conf["NAS"]["root_share_dir"];
		$this->listing_file_dir = $this->conf["NAS"]["listing_file_dir"];
		$this->root_path = ($root_path == null) ? $this->listing_file_dir : $this->root_share_dir . $root_path;
	}
	private function start_time(){
		// Start execution time statistics
		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$GLOBALS["starttime"] = $mtime;
	}
	private function end_time(){
		// End execution time statistics
		$mtime = microtime();
		$mtime = explode(" ", $mtime);
		$mtime = $mtime[1] + $mtime[0];
		$endtime = $mtime;
		return round($endtime - $GLOBALS["starttime"], 6);
	}
	private function http_resp($code = NULL) {
		if ($code !== NULL) {
			switch ($code) {
				case 100: $text = "Continue"; break;
				case 101: $text = "Switching Protocols"; break;
				case 200: $text = "OK"; break;
				case 201: $text = "Created"; break;
				case 202: $text = "Accepted"; break;
				case 203: $text = "Non-Authoritative Information"; break;
				case 204: $text = "No Content"; break;
				case 205: $text = "Reset Content"; break;
				case 206: $text = "Partial Content"; break;
				case 300: $text = "Multiple Choices"; break;
				case 301: $text = "Moved Permanently"; break;
				case 302: $text = "Moved Temporarily"; break;
				case 303: $text = "See Other"; break;
				case 304: $text = "Not Modified"; break;
				case 305: $text = "Use Proxy"; break;
				case 400: $text = "Bad Request"; break;
				case 401: $text = "Unauthorized"; break;
				case 402: $text = "Payment Required"; break;
				case 403: $text = "Forbidden"; break;
				case 404: $text = "Not Found"; break;
				case 405: $text = "Method Not Allowed"; break;
				case 406: $text = "Not Acceptable"; break;
				case 407: $text = "Proxy Authentication Required"; break;
				case 408: $text = "Request Time-out"; break;
				case 409: $text = "Conflict"; break;
				case 410: $text = "Gone"; break;
				case 411: $text = "Length Required"; break;
				case 412: $text = "Precondition Failed"; break;
				case 413: $text = "Request Entity Too Large"; break;
				case 414: $text = "Request-URI Too Large"; break;
				case 415: $text = "Unsupported Media Type"; break;
				case 500: $text = "Internal Server Error"; break;
				case 501: $text = "Not Implemented"; break;
				case 502: $text = "Bad Gateway"; break;
				case 503: $text = "Service Unavailable"; break;
				case 504: $text = "Gateway Time-out"; break;
				case 505: $text = "HTTP Version not supported"; break;
				default:
					exit("Unknown http status code \"" . htmlentities($code) . "\"");
				break;
			}
			$protocol = (isset($_SERVER["SERVER_PROTOCOL"]) ? $_SERVER["SERVER_PROTOCOL"] : "HTTP/1.0");
			header($protocol . " " . $code . " " . $text);
			return $text;
		} else {
			$code = (isset($code) ? $code : 200);
			return $code;
		}
	}
	private function aasort(&$array, $key) {
		$sorter = array();
		$ret = array();
		reset($array);
		foreach($array as $ii => $va) {
			$sorter[$ii] = $va[$key];
		}
		usort ($sorter, create_function('$a,$b', '
			return	is_dir ($a)
				? (is_dir ($b) ? strnatcasecmp ($a, $b) : -1)
				: (is_dir ($b) ? 1 : (
					strcasecmp (pathinfo ($a, PATHINFO_EXTENSION), pathinfo ($b, PATHINFO_EXTENSION)) == 0
					? strnatcasecmp ($a, $b)
					: strcasecmp (pathinfo ($a, PATHINFO_EXTENSION), pathinfo ($b, PATHINFO_EXTENSION))
				))
			;
		'));

	}
	private function explodeTree($array, $delimiter = "_", $baseval = false) {
		require_once($this->root_dir . "common/include/lib/mime_types.php");
		
		if (!class_exists("rsa", false)) {
			require($this->class_dir . "/rsa.class.php");
		}
		$rsa = new rsa();
		if(!is_array($array)) return false;
		$splitRE   = "/" . preg_quote($delimiter, "/") . "/";
		$returnArr = array();
		foreach ($array as $key => $val) {
			$parts = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
			$leafPart = array_pop($parts);
			
			$parentArr = &$returnArr;
			foreach ($parts as $part) {
				$part = $part;
				if (!isset($parentArr[$part])) {
					$k = -1;
					$parentArr[$part] = array();
				} elseif (!is_array($parentArr[$part])) {
					if ($baseval) {
						$parentArr[$part] = array("__base_val" => $parentArr[$part]);
					} else {
						$parentArr[$part] = array();
					}
				}
				$parentArr = &$parentArr[$part];
				
			}
			
			if (empty($parentArr[$leafPart])) {
				$k++;
				$parsed = pathinfo($leafPart);
				if(!$parsed["extension"]){
					if(str_replace($this->params["replace_uri"], "", $val["path"]) !== stripslashes(str_replace("\ ", " ", $this->params["path"]))){
						$parentArr["resources"][$k]["filetype"] = "DIRECTORY";
						$parentArr["resources"][$k]["uri"] = rawurlencode(base64_encode(trim(str_replace($this->params["replace_uri"], "", $val["path"]))));
						$parentArr["resources"][$k]["filename"] = substr(strrchr($val["path"], "/"), 1);
						//$parentArr["resources"][$k]["hash"] = rawurlencode($val["hash"]);
					}
				} else {
					$parentArr["resources"][$k]["filetype"] = strtoupper($parsed["extension"]);
					$parentArr["resources"][$k]["uri"] = rawurlencode(base64_encode(trim($rsa->my_token() . str_replace("///", "//", "://" . $val["path"]))));
					$parentArr["resources"][$k]["filename"] = $parsed["basename"];
					$parentArr["resources"][$k]["icon"] = $mime_type[strtolower($parsed["extension"])]["icon"];
					//$parentArr["resources"][$k]["hash"] = rawurlencode($val["hash"]);
				}
				if(is_array($parentArr["resources"])){
					//$this->aasort($parentArr["resources"], "filetype");
				}
			} elseif ($baseval && is_array($parentArr[$leafPart])) {
				$parentArr[$leafPart]["__base_val"] = $val["path"];
			}
		}
		return $returnArr;
	}
	private function have_resource($array) {
		foreach($array as $k => $v){
			if (is_array($v)) {
				return false;
			} else {
				return true;
			}
		}
	}
	private function recursive_keys($input, $search_value = null){
		$output = ($search_value !== null ? array_keys($input, $search_value) : array_keys($input)) ;
		foreach($input as $sub){
			if(is_array($sub)){
				if($sub["resources"] == "") {
					$output = ($search_value !== null ? array_merge($output, $this->recursive_keys($sub, $search_value)) : array_merge($output, $this->recursive_keys($sub))) ;
				}
			}
		}
		return $output ;
	}
	private function array_search_key($needle_key, $array ) {
		foreach($array AS $key=>$value){
			if($key == $needle_key) { return $value; }
			if(is_array($value)){
				if(($result = $this->array_search_key($needle_key,$value)) !== false) {
					return $result;
				}
			}
		}
		return false;
	} 
	private function plotTree($arr, $indent = 0){
		if (!class_exists("rsa", false)) {
			require($this->class_dir . "/rsa.class.php");
		}
		$rsa = new rsa();
		$f = -1;
		$g = -1;
		foreach($arr as $k => $v){
			$rank = (int)$indent;
			$g++;
			// skip the baseval thingy. Not a real node.
			if($k == "__base_val") continue;
			// determine the real value of this node.
			$show_val = (is_array($v) ? $v["__base_val"] : $v);
			// show the indents
			//print str_repeat("  ", $indent);
			if(is_array($v) && $k != "resources"){
				$f++;
				if($indent > 0){
					$plot[$f]["rank"] = $rank;
					$plot[$f]["label"] = $k;
						$c = $this->array_search_key("uri", $v);
						$cc = explode("/", base64_decode(rawurldecode($c["uri"])));
						$ccc = $this->recursive_keys($cc, $k);
						$c4 = array();
						for($i = 0; $i <= $ccc[0]; $i++) {
							$c4[] = $cc[$i];
						}
					$plot[$f]["uri"] = rawurlencode(base64_encode(trim(implode("/", $c4))));
					if(!is_array($this->plotTree($v, ($indent+1)))) {
						$plot[$f]["children"] = array();
					} else {
						$plot[$f]["children"] = $this->plotTree($v, ($indent+1));
					}
					if(is_array($v["resources"])){
						sort($v["resources"]);
						if(trim($v["resources"][$g]["filetype"]) == ""){
							unset($v["resources"][$g]);
						}
						$plot[$f]["resources"] = $v["resources"];
					} else {
						$plot[$f]["resources"] = array();
					}
				} else {
					$plot["rank"] = $rank;
					$plot["label"] = $k;
					$plot["children"] = $this->plotTree($v, ($indent+1));
					if(trim($v["resources"][0]["filetype"]) !== ""){
						$plot["resources"] = $v["resources"];
						$plot["children"] = array();
					} else {
						$plot["resources"] = array();
					}
				}
			}
		}
		return $plot;
	}
	private function iterate_array($array){
		$resourcetry = $this->explodeTree($array, "/");
		
		if ($this->params["path"] == "" || $this->params["path"] == "/") {
			$results_array["resultlabel"] = "Directory principale";
		} else {
			$results_array["resultlabel"] = str_replace("\ ", " ", stripslashes($this->params["labels"]));
		}
		$results_array["exactresult"] = ($this->params["op"] == "exactquery") ? true : false;
		$results_array["nresults"] = count($array);
		$results_array["resourcetrie"] = $this->plotTree($resourcetry);
		
		$response = $results_array;
		
		$results_array["resourcetrie"];
		return $response;
	}
	
	/* class functions */
	public function get_response_code($type){
		$response_code = $this->http_resp();
		switch($type) {
			case "array":
				$resp_code["responsen"] = $response_code;
				$resp_code["response"] = $this->http_resp($response_code);
				return $resp_code;
				break;
			case "code":
				return $response_code;
				break;
			case "text":
				return $this->http_resp($response_code);
				break;
		}
	}
	public function scan($type) {
		if (!class_exists("rsa", false)) {
			require($this->class_dir . "/rsa.class.php");
		}
		$rsa = new rsa();
		if(!file_exists($this->root_dir . "API/listing")){
			ob_start();
			require_once($this->root_dir . "scan.php");
			$data = ob_get_clean();
			ob_end_clean();
		}
		$scanned_files = shell_exec("cat " . $this->root_dir . "API/listing");
		$local_conf_lines = explode("\n", $scanned_files);
		
		switch($type){
			case "count":
				return count($local_conf_lines);
			break;
		}
	}
	public function get() {
		if(strlen($this->params["op"]) > 0 && trim($this->params["op"]) !== "") {
			switch($this->params["op"]){
				case "resourcestats":
					$this->response = $this->resourcestats($params);
					break;
				case "browse":
					$this->response = $this->browse($params);
					break;
				case "whatsnew":
					$this->response = $this->whatsnew($params);
					break;
				case "detail":
					$this->response = $this->details($params);
					break;
				case "exactquery":
				case "query":
				default:
					$this->response = $this->query();
					break;
			}
		}
		if($this->params["debug"] == "true"){
			print_r($this->response);
		}
		return json_encode($this->response);
	}
	
	public function set_params($params){
		if(!is_array($params)){
			return "No valid params";
		} else {
			if($this->get_response_code("code") == 200){
				// Retrieve relative root path
				$params["root_dir"] = ((trim($params["root_dir"]) !== "") ? $params["root_dir"] : $this->root_path);
				$params["nresults"] = ((strlen(trim($params["nresults"])) > 0) ? $params["nresults"] : 200);
				
				foreach($params as $pk => $pv){
					$config[$pk] = $pv;
				}
			}
			if(!is_array($this->params)){
				$this->params = $config;
			} else {
				$this->params += $params;
			}
		}
	}
	
	private function resourcestats(){
		$this->start_time();
		// Retrieve current response header
		$response = $this->get_response_code("array");
		// Retrieve count of scanned fils
		$response["result"] = $this->scan("count");
		
		$response["searchtime"] = $this->end_time();
		return $response;
	}
	private function browse(){
		$this->start_time();
		// Retrieve current response header
		$response = $this->get_response_code("array");
		$_get = preg_replace("/^\//", "", str_replace(preg_replace("/^\//", "", $this->conf["NAS"]["replace_remote_dir_with"]), "", preg_replace("/^\//", "", $this->params["path"])));
		$this->params["path"] = $this->conf["NAS"]["replace_remote_dir_with"] . "/" . $_get;
		$this->set_params(array("labels" => strtoupper($_get)));
		
		if(isset($this->params["path"]) && trim($this->params["path"]) !== "" && trim($this->params["path"]) !== "/"){
			$list = explode("\n", shell_exec("find " . $this->params["path"] . " -maxdepth 1"));
			foreach($list as $k => $v){
				if($_get == "" || $_get == "/") {
					if(!in_array($v, $this->conf["NAS"]["nas_shares"])) {
						unset($list[$k]);
					}
				}
				if($v == $this->params["path"]) {
					unset($list[$k]);
				}
			}
			$config_list = array_values(array_filter(str_replace($this->params["replace_path"], $this->params["replace_uri"], $list)));
		} else {
			$config = $this->conf;
			$config_list = str_replace($this->params["replace_path"], $this->params["replace_uri"], $this->conf["NAS"]["nas_shares"]);
		}
		foreach($config_list as $list){
			$listing[$list] = $list;
		}
		krsort($listing);
		$response["nresults"] = count($listing);
		$response["nlabels"] = count(explode(" ", $_get));
		$response["results"] = array($this->iterate_array($listing));
		$response["searchtime"] = $this->end_time();
		return $response;
	}
	private function get_last_news($days = 0){
		$whatsnew = shell_exec("find " . escapeshellcmd($this->params["replace_path"]) . " -type f -mtime -" . $days . " | sort -n");
		if(strlen($whatsnew) == 0){
			return $this->get_last_news($days+1);
		} else {
			return $whatsnew;
		}
	}
	private function whatsnew(){
		$this->start_time();
		// Retrieve current response header
		$response = $this->get_response_code("array");
		$whatsnew = $this->get_last_news();
		$_get = explode("\n", $whatsnew);
		
		foreach($_get as $line) {
			if(strlen(trim($line)) > 0) {
				$wnn = str_replace($this->params["replace_path"], $this->params["replace_uri"], $line);
				$wn_arr[$wnn] = $wnn;
			}
		}
		$response["nresults"] = is_array($wn_arr) ? count($wn_arr) : 0;
		$response["nlabels"] = is_array($_get) ? count($_get) : 0;
		$response["results"] = is_array($wn_arr) ? array($this->iterate_array($wn_arr)) : null;
		//print $response;
		return $response;
	}
	private function query(){
		ob_end_flush();
		$this->start_time();
		$config = $this->conf;
		if(isset($this->params["q"]) && trim($this->params["q"]) !== "" && preg_match("/([\w+\d+]{3,})/is", $this->params["q"])) {
			$filetype = (strlen($this->params["filetype"]) > 0) ? ".*\." . str_replace(".", "", $this->params["filetype"]) : "";
			$path = (strlen($this->params["path"]) > 0) ? "^/" . trim($this->params["path"]) . "/.*" : "";
			switch($this->params["op"]){
				case "query":
					$command = 'grep -i "' . $path . $this->params["q"] . $filetype . '" ' . $config["NAS"]["listing_file_dir"] . 'listing -m ' . $this->params["nresults"];
					break;
				case "exactquery":
					$command = 'grep -i -w "' . $path . $this->params["q"] . $filetype . '" ' . $config["NAS"]["listing_file_dir"] . 'listing -m ' . $this->params["nresults"];
					break;
				case "orquery":
					$command = 'grep -i -w "' . $path . $this->params["q"] . $filetype . '" ' . $config["NAS"]["listing_file_dir"] . 'listing -m ' . $this->params["nresults"];
					break;
				case "likequery":
					$command = 'grep -i ".' . $path . $this->params["q"] . $filetype . '." ' . $config["NAS"]["listing_file_dir"] . 'listing -m ' . $this->params["nresults"];
					break;
				default:
					$response["responsen"] = 418;
					$response["response"] = 'Unknow search operation: "' . $this->params["op"] . '"';
					$response["q"] = $this->params["q"];
					$response["nresults"] = 0;
					$response["nlabels"] = count(explode(" ", $this->params["q"]));
					$response["results"] = "I'm a teapot";
					$response["searchtime"] = $this->end_time();
					return $response;
					exit();
				break;
			}
			$scanned_files = shell_exec($command);
			if($this->params["debug"] == "true"){
				print_r($command . "\n");
			}
			$scanned_files_lines = array_filter(explode("\n", $scanned_files));
			$i = -1;
			$tree = array();
			foreach($scanned_files_lines as $l){
				@ob_flush();
				usleep(100);
				$line = json_decode($l, true);
				if(is_file(str_replace("//", "/", $this->root_share_dir . $line[0]["path"]))) {
					$arrr[str_replace("//", "/", "Ninuxoo/" . $line[0]["path"])] = $line[0];
				}
			}
			if(is_array($arrr)){
				//ksort($arrr);
				$response["responsen"] = 200;
				$response["response"] = $this->params["op"];
				$response["q"] = $this->params["q"];
				$response["nresults"] = count($arrr);
				$response["nlabels"] = count(explode(" ", $this->params["q"]));
				$response["results"] = array($this->iterate_array($arrr));
			}
		} else {
			$response["responsen"] = 400;
			$response["response"] = $this->params["op"];
			$response["q"] = $this->params["q"];
			$response["nresults"] = 0;
			$response["nlabels"] = count(explode(" ", $this->params["q"]));
			$response["results"] = "Bad request";
		}
		$response["searchtime"] = $this->end_time();
		return $response;
	}
}
?>