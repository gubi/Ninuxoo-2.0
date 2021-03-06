<?php
/**
 * Logging class:
 * - contains file, write and close public methods
 * - file sets path and name of log file
 * - write writes message to the log file (and implicitly opens log file)
 * - close closes log file
 * - first call of write method will open log file implicitly
 * - message is written with the following format: [d/M/Y:H:i:s] (script name) message
 */
class Logging {
	// declare log file and file pointer as private properties
	private $log_file, $fp;
	// set log file (path and name)
	public function file($path) {
		$this->log_file = $path;
	}
	// write message to the log file
	public function write($log_type, $message) {
		// if file pointer doesn't exist, then open log file
		if (!is_resource($this->fp)) {
			$this->open();
		}
		// define current time and suppress E_WARNING if using the system TZ settings
		// (don't forget to set the INI setting date.timezone)
		$time = @date('[d M Y H:i:s]');
		// write current time, script name and message to the log file
		fwrite($this->fp, $time . " (" . $log_type . ") " . $message . PHP_EOL);
	}
	// close log file (it's always a good idea to close a file when you're done with it)
	public function close() {
		fclose($this->fp);
	}
	// open log file (private method)
	private function open() {
		// in case of Windows set default log file
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			$log_file_default = 'c:/php/ninuxoo.log';
		}
		// set default log file for Linux and other systems
		else {
			$log_file_default = '/var/www/common/include/ninuxoo.log';
		}
		// define log file from file method or use previously set default
		$file = $this->log_file ? $this->log_file : $log_file_default;
		// open log file for writing only and place file pointer at the end of the file
		// (if the file does not exist, try to create it)
		$this->fp = fopen($file, 'a') or exit("Can't open $file!");
	}
}
?>