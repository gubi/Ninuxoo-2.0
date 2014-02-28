<?php
header("Content-type: text/plain; charset=utf-8");
function rndfunc($x){
	$step = 0.5;
	$multiplicand = floor( $x / $step );
	if(@($x % $step) > $step/2 ) $multiplicand++; // round up if needed
	return $step*$multiplicand;
}
$user_r = $output["user"];
$rate_r = $output["rate"];
unset($output["user"]);
unset($output["rate"]);


$config = parse_ini_file("../../conf/config.ini", true);
$general_settings = parse_ini_file("../../conf/general_settings.ini", true);
if(file_exists(str_replace("//", "/", $config["NAS"]["root_share_dir"] . "/") . ".ninuxoo_cache/rating_" . base64_encode(http_build_query($output)) . ".json")) {
	$rates_json = json_decode(file_get_contents(str_replace("//", "/", $config["NAS"]["root_share_dir"] . "/") . ".ninuxoo_cache/rating_" . base64_encode(http_build_query($output)) . ".json"), 1);
	
	if(is_array($rates_json)) {
		$i = 0;
		foreach($rates_json["ratings"] as $ku => $kv) {
			foreach($kv as $user => $user_rate) {
				if($user == $user_r) {
					$user_rate = $rate_r;
				}
				$rates["rates"][$user] = $user_rate;
				$file_rates["ratings"][] = array($user => $user_rate);
				$i += $user_rate;
			}
		}
		if(!array_key_exists($user_r, $rates["rates"])) {
			$rates["rates"][$user_r] = $rate_r;
			$file_rates["ratings"][] = array($user_r => $rate_r);
		}
		$rates["total"] = count($rates["rates"]);
	
		$rates["medium_rates"] = round($i/$rates["total"], 2);
		$rates["medium_rates_rounded"] = rndfunc($i/$rates["total"]);
	} else {
		$rates["total"] = 1;
		$rates["medium_rates"] = $rate_r;
		$rates["medium_rates_rounded"] = $rate_r;
		
		$file_rates["ratings"][] = array($user_r => $rate_r);
	}
} else {
	$rates["total"] = 1;
	$rates["medium_rates"] = $rate_r;
	$rates["medium_rates_rounded"] = $rate_r;
	
	$file_rates["ratings"][] = array($user_r => $rate_r);
}
if($general_settings["caching"]["allow_caching"] == "true" && $general_settings["caching"]["save_semantic_data"] == "true") {
	file_put_contents(str_replace("//", "/", $config["NAS"]["root_share_dir"] . "/") . ".ninuxoo_cache/rating_" . base64_encode(http_build_query($output)) . ".json", json_encode($file_rates));
}
print json_encode($rates);
?>