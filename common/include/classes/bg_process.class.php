<?php
header("Content-type: text/plain");

class bg_process {
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
?>
