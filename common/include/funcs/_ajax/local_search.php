<?php
header("Content-type: text/plain; charset=utf-8");
include("../../lib/JSON.php");

// ** Start personalizing **
$root_dir = "../../../../";
$config_ini_file = $root_dir . "config.ini";
$config = parse_ini_file($config_ini_file, 1);
$GLOBALS["replace_uri"] = "/home/0common/";
$GLOBALS["replace_uri_with"] = "smb://" . $config["NAS"]["ipv4"] . "/";
// ** End personalizing **

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
function explodeTree($array, $delimiter = "_", $baseval = false) {
	if(!is_array($array)) return false;
	$splitRE   = "/" . preg_quote($delimiter, "/") . "/";
	$returnArr = array();
	foreach ($array as $key => $val) {
		$parts    = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
		$leafPart = array_pop($parts);
		
		$parentArr = &$returnArr;
		foreach ($parts as $part) {
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
			$parentArr["resources"][$k]["filetype"] = strtoupper($parsed["extension"]);
			$parentArr["resources"][$k]["uri"] = $val;
			$parentArr["resources"][$k]["filename"] = $parsed["basename"];
		} elseif ($baseval && is_array($parentArr[$leafPart])) {
			$parentArr[$leafPart]["__base_val"] = $val;
		}
	}
	return $returnArr;
}
function have_resource($array) {
	foreach($array as $k => $v){
		if (is_array($v)) {
			return false;
		} else {
			return true;
		}
	}
}
function plotTree($arr, $indent = 0){
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
				if(!is_array(plotTree($v, ($indent+1), false))) {
					$plot[$f]["children"] = array();
				} else {
					$plot[$f]["children"] = plotTree($v, ($indent+1), false);
				}
				if(trim($v["resources"][0]["filetype"]) == ""){
					unset($v["resources"][0]);
				}
				if(is_array($v["resources"])){
					$plot[$f]["resources"] = $v["resources"];
				} else {
					$plot[$f]["resources"] = array();
				}
			} else {
				$plot["rank"] = $rank;
				$plot["label"] = $k;
				$plot["children"] = plotTree($v, ($indent+1), false);
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
if(isset($_GET["op"]) && trim($_GET["op"]) !== "") {
	switch($_GET["op"]){
		case "resourcestats":
			//{"responsen": 200, "response": "OK", "result": 131208}
			$response["responsen"] = 200;
			$response["response"] = "OK";
			
			if(!file_exists($root_dir . "API/listing")){
				ob_start();
				require($root_dir . "scan.php");
				$data = ob_get_clean();
				ob_end_clean();
			}
			$scanned_files = file_get_contents($root_dir . "API/listing");
			$local_conf_lines = explode("\n", $scanned_files);
			$response["result"] = count($local_conf_lines);
			break;
		case "browse":
			// Start execution time statistics
			$mtime = microtime();
			$mtime = explode(" ", $mtime);
			$mtime = $mtime[1] + $mtime[0];
			$starttime = $mtime;
			
			$response["responsen"] = 200;
			$response["response"] = "OK";
			
			$listing_list = file_get_contents($root_dir . "API/listing");
			
			$_get = preg_replace("/^\//", $GLOBALS["replace_uri"], $_GET["path"]);
			if($_get == $GLOBALS["replace_uri"] || trim($_get) == ""){
				$matched[0] = $config["NAS"]["smb_shares"];
			} else {
				preg_match_all("/^" . str_replace("/", "\/", escapeshellcmd($_get)) . ".*/im", $listing_list, $matched);
			}
			if(is_array($matched[0]) && count($matched[0]) > 0) {
				$scanned_files_lines = array_filter($matched[0]);
				//print_r($scanned_files_lines);
				sort($scanned_files_lines);
				foreach($scanned_files_lines as $line){
					$linee = str_replace($GLOBALS["replace_uri"], $GLOBALS["replace_uri_with"], $line);
					$arrr[$linee] = $linee;
				}
				$resourcetry = explodeTree($arrr, "/");
				// End execution time statistics
				$mtime = microtime();
				$mtime = explode(" ", $mtime);
				$mtime = $mtime[1] + $mtime[0];
				$endtime = $mtime;
				$totaltime = round($endtime - $starttime, 6);
				
				$response["nresults"] = count($arrr);
				$response["nlabels"] = count(explode(" ", $_get));
				$response["searchtime"] = $totaltime;
					if ($root_path == $GLOBALS["replace_uri"]) {
						$results_array["resultlabel"] = "Directory principale";
					} else {
						$results_array["resultlabel"] = strtoupper($_get);
					}
					$results_array["exactresult"] = false;
					$results_array["nresults"] = count($arrr);
					$results_array["resourcetrie"] = plotTree(array("" => $resourcetry));
				$response["results"] = array($results_array);
			
			} else {
				$results_array["exactresult"] = false;
				$results_array["nresults"] = count($arrr);
				$response["results"] = array($results_array);
			}
		break;
		case "whatsnew":
			require_once '../../lib/PEAR/Text/Diff.php';
			require_once '../../lib/PEAR/Text/Diff/Renderer.php';
			require_once '../../lib/PEAR/Text/Diff/Renderer/inline.php';

			$response["responsen"] = 200;
			$response["response"] = "OK";
			if ($handle = opendir($root_dir . "API/~listing_history")) {
				$handle_arr = readdir($handle);
				if(count($handle_arr) == 0){
					$response["result"] = 0;
				} else {
					$all_files = scandir($root_dir . "~listing_history", 1);
					$last_file = $all_files[0];
					$fp = fopen($root_dir . "API/~listing_history/" . $last_file, 'r');
					$content = fread($fp, filesize($root_dir . "API/~listing_history/" . $last_file));
					fclose($fp);
					$fp = fopen($root_dir . "API/listing", 'r');
					$content2 = fread($fp, filesize($root_dir . "API/listing"));
					fclose($fp);
					$sp_content = explode("\n", $content);
					$sp_content2 = explode("\n", $content2);
					
					$diff = new Text_Diff('auto', array($sp_content, $sp_content2));
					$r_inline = new Text_Diff_Renderer_inline(
					    array(
						'ins_prefix' => '<span>',
						'ins_suffix' => '</span>',
						'del_prefix' => '[',
						'del_suffix' => ']'
					    )
					);
					$render = trim($r_inline->render($diff));
					preg_match_all("/\<([\w]+)[^>]*>(.*?)<\/span>/is", $render, $matched_diff);
					$scanned_files_lines = array_filter(explode("\n", $matched_diff[2][0]));
					foreach($scanned_files_lines as $line){
						$linee = str_replace($GLOBALS["replace_uri"], $GLOBALS["replace_uri_with"], $line);
						$arrr[$linee] = $linee;
					}
					$resourcetry = explodeTree($arrr, "/");
					$results_array["nresults"] = count($arrr);
					$results_array["resourcetrie"] = plotTree(array("" => $resourcetry));
					$response["results"] = array($results_array);
				}
			}
			break;
		case "query":
		default:
			if(isset($_GET["q"]) && trim($_GET["q"]) !== "") {
				// Start execution time statistics
				$mtime = microtime();
				$mtime = explode(" ", $mtime);
				$mtime = $mtime[1] + $mtime[0];
				$starttime = $mtime;

				$term = urldecode($_GET["q"]);
				if(file_exists($config_ini_file)){
					if(isset($_GET["filetype"]) && trim($_GET["filetype"]) !== "" && trim($_GET["filetype"]) !== "undefined"){
						$search_param = ' -and -iname "*.' . $_GET["filetype"] . '"';
					}
					switch($_GET["op"]){
						case "exactquery":
							$exactresult = true;
							$scanned_files = shell_exec('find ' . $GLOBALS["replace_uri"] . ' -name "*' . $term . '*"' . $search_param);
							break;
						case "query":
							$exactresult = false;
							$scanned_files = shell_exec('find ' . $GLOBALS["replace_uri"] . ' -iname "*' . $term . '*"' . $search_param);
							break;
						break;
					}
					$scanned_files_lines = array_filter(explode("\n", $scanned_files));
					//print_r($scanned_files_lines);
					$i = -1;
					$tree = array();
					foreach($scanned_files_lines as $line){
						if(stripos($line, $term) > 0){
							$i++;
							similar_text($term, $line, $percent);
							$percent = "" . round($percent, 2);
							$res[$percent] = $line;
							$file_data = pathinfo($line);
							$linee = str_replace($GLOBALS["replace_uri"], $GLOBALS["replace_uri_with"], $line);
							$arrr[$linee] = $linee;
						}
					}
					$resourcetry = explodeTree($arrr, "/");
					if(is_array($res)){
						krsort($res);
						$results_array["resultlabel"] = strtoupper($term);
						$results_array["nresults"] = count($res);
						$results_array["resourcetrie"] = plotTree(array("" => $resourcetry));
						$results_array["exactresult"] = true;
					} else {
						//print "No results found";
					}
					// End execution time statistics
					$mtime = microtime();
					$mtime = explode(" ", $mtime);
					$mtime = $mtime[1] + $mtime[0];
					$endtime = $mtime;
					$totaltime = round($endtime - $starttime, 6);
					
					$response["q"] = $term;
					$response["nresults"] = count($res);
					$response["nlabels"] = count(explode(" ", $term));
					$response["searchtime"] = $totaltime;
					$response["responsen"] = 200;
					$response["response"] = "OK";
					$response["results"] = array($results_array);
				} else {
					$response["responsen"] = 200;
					$response["response"] = "no config.ini file: need scanning";
					$response["result"] = 0;
					$response["results"]  = array();
				}
			}
		break;
	}
}
//print_r(plotTree($resourcetrie));
if($_GET["debug"] == "true"){
	print_r($response);
	//print_r($response);
}
print json_encode($response);
?>