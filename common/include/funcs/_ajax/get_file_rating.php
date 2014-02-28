<?php
header("Content-type: text/plain; charset=utf-8");
function rndfunc($x){
	$step = 0.5;
	$multiplicand = floor( $x / $step );
	if(@($x % $step) > $step/2 ) $multiplicand++; // round up if needed
	return $step*$multiplicand;
}

$config = parse_ini_file("../../conf/config.ini", true);
if(file_exists(str_replace("//", "/", $config["NAS"]["root_share_dir"] . "/") . ".ninuxoo_cache/rating_" . base64_encode(http_build_query($output)) . ".json")) {
	$rates_json = json_decode(file_get_contents(str_replace("//", "/", $config["NAS"]["root_share_dir"] . "/") . ".ninuxoo_cache/rating_" . base64_encode(http_build_query($output)) . ".json"), 1);
	if(is_array($rates_json)) {
		$rates["total"] = count($rates_json["ratings"]);
		$i = 0;
		foreach($rates_json["ratings"] as $ku => $kv) {
			foreach($kv as $user => $user_rate) {
				$rates["rates"][$user] = $user_rate;
				$i += $user_rate;
			}
		}
		$rates["medium_rates"] = round($i/$rates["total"], 2);
		$rates["medium_rates_rounded"] = rndfunc($i/$rates["total"]);
	} else {
		$rates["total"] = 0;
		$rates["medium_rates"] = 0;
		$rates["medium_rates_rounded"] = 0;
	}
} else {
	$rates["total"] = 0;
	$rates["medium_rates"] = 0;
	$rates["medium_rates_rounded"] = 0;
}
print json_encode($rates);
?>