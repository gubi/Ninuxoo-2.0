<?php
header("Content-type: text/plain");

class BackgroundProcess {
	private $command;
	private $pid;
	
	public function __construct($command) {
		$this->command = $command;
	}
	public function run() {
		$this->pid = shell_exec(sprintf('%s > /dev/null 2>&1 & echo $!', $this->command));
		
	}
	public function isRunning() {
		try {
			$result = shell_exec(sprintf('ps %d', $this->pid));
			if(count(preg_split("/\n/", $result)) > 2) {
				return true;
			}
		} catch(Exception $e) {}
		
		return false;
	}
	public function getPid() {
		return $this->pid;
	}
}
$user_config = parse_ini_file("../../conf/user/" . sha1($output["user_name"]) . "/user.conf", true);
if($user_config["Chat"]["show_ip"] == "false") {
	$noip = "noip:";
} else {
	$noip = "";
}
$kill = shell_exec('ps aux | grep -i "' . $output["host"] . '" | awk {\'print $2\'} | xargs kill -9');
$cmd = 'avahi-publish -s "' . $output["host"] . '" _dns-sd._tcp 64689 "' . str_replace(";", "{~}", str_replace("noip:noip:", "noip:", $noip . $output["message"])) . '"';
$process = new BackgroundProcess($cmd);
$process->run();
print $process->getPid();
?>