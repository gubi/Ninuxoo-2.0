<?php
header("Content-type: text/plain;");
if($_GET["clean"] == "true"){
	exit();
}
include("../browser.php");
include("Text/Wiki.php");
include("../../lib/JSON.php");
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
if (!function_exists("http_build_query")) { 
	function http_build_query($data, $prefix="", $sep="", $key="") { 
		$ret = array(); 
		foreach ((array)$data as $k => $v) { 
			if (is_int($k) && $prefix != null) { 
				$k = urlencode($prefix . $k); 
			} 
			if ((!empty($key)) || ($key === 0))  $k = $key."[".urlencode($k)."]"; 
			if (is_array($v) || is_object($v)) { 
				array_push($ret, http_build_query($v, "", $sep, $k)); 
			} else { 
				array_push($ret, $k."=".urlencode($v)); 
			}
		} 
		if (empty($sep)) $sep = ini_get("arg_separator.output"); 
		return implode($sep, $ret); 
	}
}
function isUTF8($string) {
	return (utf8_encode(utf8_decode($string)) == $string);
}
function browse_wiki($title, $type = "false", $year = 0, $bkp_year = "", $lang = "it") {
	@ob_flush();
	$wikipedia_main_url = "http://" . $lang . ".wikipedia.org";
	$url = $wikipedia_main_url . "/w/api.php?";
	if(trim($type) !== "false"){
		if((int)($year) > 0){
			if($lang == "it"){
				$spec = " (" . strtolower($type) . " " . $year . ")";
			} else {
				$spec = " (" . $year . " " . strtolower($type) . ")";
			}
		} else {
			$spec = " (" . strtolower($type) . ")";
		}
	} else {
		$spec = "";
	}
	
	$options = array(
		"format"=>"php",
		"action" => "query",
		"titles"=> $title . $spec,
		"prop" => "revisions",
		"rvprop" => "content"
		);
	$url .= http_build_query($options, "", "&");
	// Title (year film)
	$content = browse($url);
		//usleep(1000);
		//print $spec . "\n" . str_repeat("-", 100) . "\n\n";
	if(strpos($content, "missing")){
		if($lang == "it"){
			if((int)$year > 0){
				// Title (film)
				return browse_wiki($title, $type, 0, $year, "it");
			} else {
				if((int)$year == 0 && $type == "false"){
					// Title (year film)
					return browse_wiki($title, $type, $bkp_year, $bkp_year, "en");
				} else {
					// Title
					return browse_wiki($title, "false", 0, $year, "it");
				}
			}
		} else {
			if((int)$year > 0){
				// Title (film)
				return browse_wiki($title, $type, 0, $bkp_year, "en");
			} else {
				if((int)$year == 0 && $type == "false"){
					$content_arr["content"] = "none";
					return $content_arr;
				} else {
					// Title
					return browse_wiki($title, "false", 0, $year, "it");
				}
			}
			if((int)$year > 0){
				return browse_wiki($title, $type, $bkp_year, $bkp_year, "en");
			}
		}
	} else {
		$content_arr["url"] = $url;
		$content_arr["lang"] = (!$type ? "it" : $lang);
		$content_arr["content"] = $content;
		return $content_arr;
	}
}
function grab_file_image($lang, $value){
	$wikipedia_main_url = "http://" . $lang . ".wikipedia.org";
	
	$wiki_img_file_url = $wikipedia_main_url . "/wiki/File:" . str_replace(" ", "_", $value);
	$wiki_img_file_content = browse($wiki_img_file_url);
	preg_match_all('/< *img[^>]*src *=*["\']?([^"\']*)/i', $wiki_img_file_content, $matched_wiki_img_file_content);
	
	return "http:" . $matched_wiki_img_file_content[1][0];
}
$title = preg_replace("/ \[.*?\]/", "", trim(urldecode($_GET["title"])));
$content = browse_wiki($title, $_GET["type"], $_GET["year"]);
if($content["content"] !== "none"){
	//print_r($content); exit();
	// Parse synopsis data
	$no_link = preg_replace("#\[\[(.*?)(?:\|[\w ]+)?\]\]#is", "$1", $content["content"]);
	preg_match_all("#{{(.*?)}}#s", $no_link, $match_sinottico);
	// Clean
	$match_sinottico = preg_replace("#(\w+)\|(\w+)#s", "$1 $2", preg_replace("#\=(\s+|)(\n+|\r+\v+)#s", "=", $match_sinottico[1][0]));
	preg_match_all("#[\|](.*?)[\=](.*?)\n#s", $match_sinottico, $matched_sinottico);
	if(count($matched_sinottico[1]) > 0) {
		$sinottico["lang"] = $content["lang"];
		foreach($matched_sinottico[1] as $id => $val){
			$key = str_replace("|", "", trim($matched_sinottico[1][$id]));
			$value = str_replace("|", "", (!strpos($matched_sinottico[2][$id], ",") ? $matched_sinottico[2][$id] : (count(explode(", ", ($matched_sinottico[2][$id]))) <= 1) ? trim($matched_sinottico[2][$id]) : array_map("trim", explode(", ", ($matched_sinottico[2][$id])))));
			if($key == "immagine" || $key == "image"){
				$value = grab_file_image($content["lang"], $value);
			}
			$sinottico[$key] = $value;
		}
	} else {
		preg_match_all('/File:(.*?)\|/i', $content["content"], $matched_wiki_img_file_link);
		$sinottico["immagine"] = grab_file_image($content["lang"], $matched_wiki_img_file_link[1][0]);
	}
	$wiki_content["sinossi"] = $sinottico;
	//print_r($wiki_content); exit();
	// Clean content
	$content = str_replace(array("{{", "}}"), "", preg_replace("#<!--.*?-->#s", "", preg_replace("#(\=\=(\s+|)Note(\s+|)\=\=.*)#s", "", preg_replace("/^.+\n/", "", preg_replace("#({{.*?}})#s", "", $content)))));
	$content = str_replace(array(" $", " €"), array("$", "€"), $content);
	$wiki = & Text_Wiki::factory('Mediawiki');
	$wiki->setRenderConf('xhtml', 'wikilink', 'view_url', 'http://it.wikipedia.org/wiki/');
	$wiki->setRenderConf('xhtml', 'wikilink', 'pages', false);
	if(isUTF8($content["content"])) {
		$the_content = utf8_decode($content["content"]);
	} else {
		$the_content = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $content["content"]);
	}
	//print isUTF8($content["content"]) . "\n\n" . $the_content;
	$wiki_content["contenuto"] = $wiki->transform($the_content, 'Xhtml');
	
	if(isset($_GET["debug"]) && trim($_GET["debug"]) == "true") {
		print_r($wiki_content);
	} else {
		print json_encode($wiki_content);
	}
} else {
	print "no results found";
}
?>
