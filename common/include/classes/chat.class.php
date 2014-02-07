<?php
header("Content-type: text/plain;");

class chat {
	function __construct() {
		require_once("bg_process.class.php");
	}
	private function set_error($message) {
		print $message;
		exit();
	}
	private function proc_exec($ip, $cmd) {
		header('Content-Encoding: none;');
		
		set_time_limit(0);
		$handle = popen($cmd, "r");
		if (ob_get_level() == 0) {
			ob_start();
		}
		while(!feof($handle)) {
			$buffer = fgets($handle);
			$buffer = trim($buffer);

			print $buffer . "\n" . str_pad("", 1000) . "\n";
			
			ob_flush();
			flush();
			usleep(1);
		}
		
		pclose($handle);
		ob_end_flush();
	}
	public function params($nick = null, $email = null, $personal_message = null) {
		if($nick == null || $email == null || $personal_message == null) {
			$this->set_error("invalid params");
		}
		$this->nick = $nick;
		$this->email = $email;
		$this->personal_message = $personal_message;
		$this->id = md5($email . $personal_message);
	}
	public function set_status($status) {
		if(strlen(trim($this->nick)) == 0 || !isset($this->nick) || $this->nick == null) {
			$this->set_error("no params");
		}
		$kill = shell_exec('ps aux | grep -i "' . $this->email . '" | awk {\'print $2\'} | xargs kill -9');
		$cmd = 'avahi-publish -s "' . $this->nick . '" _irc._tcp 64690 "' . $this->email . ':' . escapeshellcmd($this->personal_message) . ':' . $this->id . ':' . $status . '"';
		$process = new bg_process($cmd);
		$process->run();
		
		return (strlen($process->getPid()) > 0 && $status !== "do_not_disturb") ? true : false;
	}
	public function connect($status) {
		if($this->set_status($status)) {
			/*
			$cmd = "cryptcat -nl -p 64690 -k \"" . $this->id . "\"";
			$process = new bg_process($cmd);
			$process->run();
			*/
			//print "cryptcat -nl -p 64690 -k \"" . $this->id . "\"\n";
			$this->proc_exec($this->id, "cryptcat -nl -p 64690 -k \"" . $this->id . "\"");
		} else {
			$kill = shell_exec('ps aux | grep -i "k ' . $this->id . '" | awk {\'print $2\'} | xargs kill -9');
		}
		print "connected:" . $status;
	}
	public function disconnect() {
		$kill = shell_exec('ps aux | grep -i "' . $this->id . '" | awk {\'print $2\'} | xargs kill -9');
		print "disconnected";
	}
	public function send($ip, $password, $message) {
		$this->proc_exec($this->id, "echo \"" . $message . "\" | cryptcat -nv " . $ip . " 64690 -k \"" . $this->id . "\"");
	}
}
/*
$chat = new chat("Gubi", "gubi.ale@iod.io", "Hasta siempre!");
switch($_GET["action"]) {
	case "connect":
		$chat->connect("online");
		break;
	case "disconnect":
		$chat->disconnect();
		break;
	case "send":
		if(strlen(trim($_GET["message"])) > 0) {
			$chat->send("192.168.36.210", "b3fac954ea45c7243534ae276bd89602", $_GET["message"]);
		} else {
			print "no message to send";
		}
		break;
}
*/
?>