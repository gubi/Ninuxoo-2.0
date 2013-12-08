<?php
header("Content-type: text/plain");

$token = base64_decode($output["token"]);
if(file_exists("../../conf/trusted/" . $token . ".pem~")) {
	@chmod("../../conf/trusted/" . $token . ".pem~", 0777);
	shell_exec("mv ../../conf/trusted/" . $token . ".pem~ ../../conf/untrusted/" . $token . ".pem~ && rm ../../conf/trusted/" . $token . ".pem~");
} else if (file_exists("../../conf/trusted/" . $token . ".pem")) {
	@chmod("../../conf/trusted/" . $token . ".pem", 0777);
	shell_exec("mv ../../conf/trusted/" . $token . ".pem ../../conf/untrusted/" . $token . ".pem");
} else {
	shell_exec("touch ../../conf/untrusted/" . $token . ".pem");
	@chmod("../../conf/untrusted/" . $token . ".pem", 0777);
}
print json_encode(array("data" => "ok"));
?>