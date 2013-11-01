<?php
header("Content-type: text/plain; charset=utf-8");

function parse_browse_share($dir){
	$url = "http://ninuxoo.ninux.org/cgi-bin/browse_share.cgi?url=" . urldecode($dir);
	$browse_share = trim(file_get_contents($url));
	preg_match_all('{<div\s+id="result"\s*>(.*?)</div>}si', $browse_share, $result);
	preg_match_all("{<li\s+class='(\w+)'\s*>(.*?)</li>}si", trim($result[1][0]), $result_li);
	for($i = 0; $i < count($result_li[1]); $i++){
		preg_match_all("{<a.*href='(.*?)'.*>(.*?)</a>}si", trim($result_li[2][$i]), $result_a[$i]);
		$result_a[$i]["class"] = $result_li[1][$i];
	}
	$k = 0;
	foreach($result_a as $rk => $rv){
		$k++;
		$link = str_replace("/cgi-bin/browse_share.cgi?url=", "", $rv[1][0]);
		$link = mb_convert_encoding(str_replace($_GET["url"] . "/", "", $link), "UTF-8", "HTML-ENTITIES");
		$text = $rv[2][0];
		if($rv["class"] == "browse"){
			$res["directory"][$k]["text"] = mb_convert_encoding($text, "UTF-8", "HTML-ENTITIES");
			$res["directory"][$k]["expanded"] = "false";
			/*
			// Check recoursive subdirectories (low)
			if(array_key_exists("directory", parse_browse_share($_GET["url"] . "/" . $text))){
				$res["directory"][$k]["hasChildren"] = "true";
			} else {
				$res["directory"][$k]["hasChildren"] = "false";
			}
			*/
		} else {
			$res["file"][$k]["text"] = mb_convert_encoding($text, "UTF-8", "HTML-ENTITIES");
		}
	}
	ksort($res);
	return $res;
}
//print_r(parse_browse_share($_GET["url"]));
print json_encode(parse_browse_share($_GET["url"]));
?>