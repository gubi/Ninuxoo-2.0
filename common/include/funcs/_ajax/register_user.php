<?php
header("Content-type: text/plain");

$conf_dir = str_replace("funcs/_ajax", "conf", __DIR__);
$config = parse_ini_file($conf_dir . "/config.ini", true);
if(!file_exists($conf_dir . "/user/" . sha1($output["user_username"]))) {
	require_once("../../classes/sendmail.class.php");
	$sendmail = new Sendmail();
	
	$key = $output["user_name"] . "::::" . $output["user_username"] . "::::" . sha1($output["user_password"]) . "::::" . $output["node_name"];
	
	$message = "Ciao " . $output["user_name"] . ",\n";
	$message .= "ti e' stata inviata questa e-mail perche' e' stata richiesta la tua registrazione a Ninuxoo.\n";
	$message .= "Per poter proseguire con il processo, clicca (o copia e incolla nel tuo browser) il link a seguire:\n\n";
	$message .= "> " . $config["NAS"]["http_root"] . "/Registrati/" . urlencode(base64_encode($key . "::::" . sha1(date("d-m-Y")))) . "\n\n";
	$message .= "Il link per la validazione sara' attivo fino alla mezzanotte di oggi.";

	$sendmail->send($output["user_name"] . " <" . $output["user_username"] . ">", "Richiesta di registrazione a Ninuxoo", $message);
	
	print json_encode(array("data" => "ok"));
} else {
	print json_encode(array("data" => "user exists"));
}
?>