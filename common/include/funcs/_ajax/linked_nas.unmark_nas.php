<?php
header("Content-type: text/plain");

$token = base64_decode($output["token"]);
if(file_exists("../../conf/trusted/" . $token . ".pem~")) {
	@chmod("../../conf/trusted/" . $token . ".pem~", 0777);
	shell_exec("rm ../../conf/untrusted/" . $token . ".pem~");
}
if (file_exists("../../conf/untrusted/" . $token . ".pem")) {
	@chmod("../../conf/untrusted/" . $token . ".pem", 0777);
	shell_exec("rm ../../conf/untrusted/" . $token . ".pem");
}
print json_encode(array("data" => "ok"));
?>