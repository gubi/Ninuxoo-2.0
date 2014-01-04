<?php
function get_body($name, $username) {
	$config = parse_ini_file("../../conf/config.ini", true);
	
	$message = "Ciao " . $name . ",\n";
	$message .= "hai richiesto di poter reimpostare la tua password di accesso a Ninuxoo, e questa e-mail contiene il link per procedere al processo di reset.\n";
	$message .= "Clicca (o copia e incolla nel tuo browser) il link a seguire:\n\n";
	$message .= "> " . $config["NAS"]["http_root"] . "/Accedi/Password_dimenticata/" . urlencode(base64_encode($username . "::::" . sha1(date("d-m-Y")))) . "\n\n";
	$message .= "Il link per la validazione sara' attivo fino alla mezzanotte di oggi.";
	
	return $message;
}

if(file_exists("../../conf/user/" . sha1($output["username"]))) {
	$user_conf = parse_ini_file("../../conf/user/" . sha1($output["username"]) . "/user.conf", true);
	if($output["username"] == $user_conf["User"]["email"]) {
		require_once("../../classes/sendmail.class.php");
		
		$sendmail = new sendmail();
		$sendmail->send(ucwords($user_conf["User"]["name"]) . " <" . $output["username"] . ">", "Reset della password per Ninuxoo", get_body(ucwords($user_conf["User"]["name"]), $output["username"]));
		
		print json_encode(array("error" => false, "message" => "&Egrave; stata spedita una mail con il link di attivazione"));
	} else {
		// Fare il check su altri NAS prima
		print json_encode(array("error" => true, "message" => "Username inesistente"));
	}
} else {
	// Fare il check su altri NAS prima
	print json_encode(array("error" => true, "message" => "Username inesistente"));
}
?>