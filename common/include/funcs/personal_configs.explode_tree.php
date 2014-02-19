<?php
function explodeTree($array, $delimiter = "_", $baseval = false) {
	if(!is_array($array)) return false;
	$splitRE = "/" . preg_quote($delimiter, "/") . "/";
	$returnArr = array();
	foreach ($array as $key => $val) {
		// Get parent parts and the current leaf
		$parts = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
		$leafPart = array_pop($parts);
		
		// Build parent structure
		// Might be slow for really deep and large structures
		$parentArr = &$returnArr;
		foreach ($parts as $part) {
			if (!isset($parentArr[$part])) {
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
		
		// Add the final part to the structure
		if (empty($parentArr[$leafPart])) {
			$parentArr[$leafPart] = $val;
		} elseif ($baseval && is_array($parentArr[$leafPart])) {
			$parentArr[$leafPart]["__base_val"] = $val;
		}
	}
	return $returnArr;
}
function plotTree($arr, $indent = 0, $mother_run = true, $dir_name, $mime_type){
	print '<tbody>';
	foreach($arr["."] as $dir => $path) {
		if(is_dir($dir)) {
			if(is_array($path)) {
				$info = pathinfo($dir);
				print '<tr id="' . md5($dir) . '"><td class="fa fa-fw"><a href="javascript:void(0);" id="" class="remove_item text-danger" style="display: none;"><small class="fa fa-times fa-fw"></small></a></td><td><span class="fa fa-caret-right fa-fw"></span><span class="fa fa-folder-o"></span>&nbsp;&nbsp;<a href="javascript:void(0);" class="dir_collapse dirname" id="./' . $dir . '" title="Espandi la directory">' . $dir . '</a>&nbsp;&nbsp;<a href="javascript:void(0);" class="edit_item text-muted" style="display: none;"><span class="fa fa-edit fa-fw"></span></a></td><td style="color: #999;">Directory <small>(' . count($path) . ((count($path) == 1) ? ' elemento' : ' elementi') . ')</small></td></tr>';
				print '<tbody class="' . md5($info["basename"]) . ' collapsible collapsed" style="display: none;">';
				foreach($path as $current => $complete) {
					if(is_file($complete)) {
						$info2 = pathinfo($current);
						print '<tr id="' . md5($current) . '"><td class="fa fa-fw"><a href="javascript:void(0);" id="" class="remove_item text-danger" style="display: none;"><small class="fa fa-times fa-fw"></small></a></td><td>&emsp;&emsp;<span class="' . $mime_type[$info2["extension"]]["icon"] . '"></span>&nbsp;&nbsp;<a class="dirname" id="' . $complete . '" href="./Admin/Config_editor/' . base64_encode($dir_name . "/" . str_replace("./", "", $complete)) . '">' . $current . '</a></td><td style="color: #999;">' . (is_dir($dir_name . "/" . $info2["basename"]) ? 'Directory' : $mime_type[$info2["extension"]]["text"]) . '</td></tr>';
					}
				}
				print '</tbody>';
			} else {
				$info3 = pathinfo($dir);
				print '<tr id="' . md5($dir) . '"><td class="fa fa-fw"><a href="javascript:void(0);" id="" class="remove_item text-danger" style="display: none;"><small class="fa fa-times fa-fw"></small></a></td><td><span class="fa fa-caret-right fa-fw text-muted"></span><span class="fa fa-folder-o text-muted"></span>&nbsp;&nbsp;<span class="text-muted dirname" id="' . $path . '">' . $info3["basename"] . '</span>&nbsp;&nbsp;<a href="javascript:void(0);" class="edit_item text-muted" style="display: none;"><span class="fa fa-edit fa-fw"></span></a></td><td style="color: #999;">Directory <small>(vuota)</small></td></tr>';
			}
		} else {
			$info4 = pathinfo($dir);
			print '<tr id="' . md5($dir) . '"><td class="fa fa-fw"><a href="javascript:void(0);" id="" class="remove_item text-danger" style="display: none;"><small class="fa fa-times fa-fw"></small></a></td><td>&nbsp;<span class="' . $mime_type[$info4["extension"]]["icon"] . '"></span>&nbsp;&nbsp;<a id="' . $path . '" class="dirname" href="./Admin/Config_editor/' . base64_encode($dir_name . "/" . $info4["basename"]) . '">' . $dir . '</a></td><td style="color: #999;">' . (is_dir($dir_name . "/" . $info4["basename"]) ? 'Directory' : $mime_type[$info4["extension"]]["text"]) . '</td></tr>';
		}
	}
	print '</tbody>';
}
?>