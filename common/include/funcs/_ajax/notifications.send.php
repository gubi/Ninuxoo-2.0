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

$kill = shell_exec('ps aux | grep -i "' . $output["host"] . '" | awk {\'print $2\'} | xargs kill -9');
$cmd = 'avahi-publish -s "' . $output["host"] . '" _dns-sd._tcp 64689 "' . str_replace(";", "{~}", $output["message"]) . '"';
$process = new BackgroundProcess($cmd);
$process->run();
print $process->getPid();
?>