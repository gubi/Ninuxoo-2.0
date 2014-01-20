<?php
header("Content-type: text/plain");
require_once("../../classes/rsa.class.php");
require_once("../../classes/scan.class.php");

$rsa = new rsa();
$pubkey = file_get_contents("../../conf/rsa_2048_pub.pem");
$token = trim($rsa->get_token($pubkey));
$decrypted = trim($rsa->simple_private_decrypt(base64_decode($output["token"])));

if($decrypted == $token) {
	$scan = new scan();
	$scan->save("json");
}
?>