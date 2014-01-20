<?php
header("Content-type: text/plain");

require_once("../../classes/rsa.class.php");
$rsa = new rsa();
$hash = rawurldecode($output["dir"]);
$file = trim($rsa->simple_decrypt($hash));

$locale = "it_IT.UTF-8";
setlocale(LC_ALL, $locale);
putenv("LC_ALL=" . $locale);
$ls = explode("\n", trim(shell_exec("ls -B -N " . str_replace(" ", "\ ", escapeshellcmd($file)))));

foreach($ls as $dir) {
	if(is_dir($file . "/" . $dir)) {
		$thumbs = glob($file . "/" . $dir . "/*.{jpg,jpeg,png,gif}", GLOB_BRACE);
		if(count($thumbs) > 0) {
			foreach($thumbs as $img) {
				$imgs[$dir][] = $img;
				/*
				if(strpos(strtolower($img), "cover") !== false || strpos(strtolower($img), "front") !== false || strpos(strtolower($img), "folder") !== false) {
					$imgs[$dir][0] = $img;
				} else {
					$imgs[$dir][] = $img;
				}
				if(count($imgs) == 0) {
					$imgs[$dir][] = $img;
				}
				*/
			}
			$max_rand = count($imgs[$dir]);
			if($max_rand == 1) {
				$im[md5($dir)]["src"] = rawurlencode($rsa->simple_encrypt($imgs[$dir][0]));
			} else {
				if($max_rand > 1) {
					$im[md5($dir)]["src"] = rawurlencode($rsa->simple_encrypt($imgs[$dir][rand(0, ($max_rand - 1))]));
				} else {
					$im[md5($dir)]["src"] = null;
				}
			}
		} else {
		
		}
		$im[md5($dir)]["data"] = $dir;
	} else {
		$info = pathinfo($file . "/" . $dir);
		switch(strtolower($info["extension"])) {
			case "pdf":
				break;
		}
	}
}
print json_encode($im);
?>