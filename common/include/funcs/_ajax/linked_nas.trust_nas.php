<?php
header("Content-type: text/plain");
require_once("../../classes/mdns.class.php");
require_once("../../classes/link_nas.class.php");

$output = $_GET;

function sendmail($to_name, $to_mail) {
	$name = explode(" ", $to_name);
	return mail($to_name . " <" . $to_mail . ">", "Richiesta di connessione del NAS", "Ciao " . ucfirst($name[0]) . ",\n", "From: Ninuxoo <ninuxoo@ninux.org>\r\nX-Mailer: PHP/" . phpversion());
}
$mdns = new mdns();
$trusted = $mdns->check_trusted();

if(is_array($trusted) && in_array(base64_decode($output["token"]), $trusted)) {
	print json_encode(array("data" => "ok"));
} else {
	// Detect owner
	$ndata = $mdns->scan(true, base64_decode($output["token"]));
	if(is_array($ndata)) {
		foreach($ndata as $hostname => $data) {
			$to_name = $data["owner"]["name"];
			$to_mail = $data["owner"]["email"];
			
			$addr = $data["address"];
		}
		//print $to . "\n";
		//print json_encode(array("data" => $output["message"]));
		//print_r($addr);
		foreach($addr as $k => $ip) {
			$d = @file_get_contents("http://" . $ip . "/API/index.php?action=make_friend&ip=" . $_SERVER["SERVER_ADDR"]);
			print "http://" . $ip . "/API/index.php?action=make_friend&ip=" . $_SERVER["SERVER_ADDR"];
			if(strlen($d) > 0) {
				if(sendmail($to_name, $to_mail)) {
					$ln = new link_nas();
					$request = $ln->third_response($d);
					if($request !== "Time expired") {
						print json_encode(array("data" => "ok"));
					} else {
						print json_encode(array("data" => "error::Time expired"));
					}
				} else {
					print json_encode(array("data" => "error::Mail not sent"));
				}
				exit();
			}
		}
		if(strlen($d) == 0) {
			print json_encode(array("data" => "error::Connection refused"));
		}
	} else {
		print json_encode(array("data" => "error::No token detected"));
	}
}
?>