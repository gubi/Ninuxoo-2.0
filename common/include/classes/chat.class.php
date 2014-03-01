<?php
/**
* Ninuxoo 2.0
*
* PHP Version 5.3
*
* @copyright 2013-2014 Alessandro Gubitosi / Gubi (http://iod.io)
* @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3
* @link https://github.com/gubi/Ninuxoo-2.0
*/

/**
* A class for chat with other users
*
* _
*
* @package	Ninuxoo 2.0
* @author		Alessandro Gubitosi <gubi.ale@iod.io>
* @license 	http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3
* @access		public
* @link		https://github.com/gubi/Ninuxoo-2.0/blob/master/common/include/classes/chat.class.php
* @uses		bg_process.class.php Bg_process class
* @todo		This class is in developing status and must be completed
*/
class chat {
	/**
	* Construct
	*
	* Initialize the class
	*
	* @uses bg_process.class.php bg_process class
	* @return void
	*/
	function __construct() {
		require_once("bg_process.class.php");
	}
	/**
	* Display error message and exit
	*
	* @param string $message The error message to display
	* @return void
	*/
	private function set_error($message) {
		print $message;
		exit();
	}
	/**
	* Wrapper for proc_*() functions
	*
	* Taken from http://it1.php.net/proc_open#95207
	*
	* @param string $id User id
	* @param string $cmd Command
	* @return void
	* @access private
	*/
	private function proc_exec($id, $cmd) {
		header("Content-Encoding: none;");
		
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
	/**
	* Set params
	*
	* @param null|string $nick User nickname
	* @param null|string $email User email
	* @param null|string $personal_message User personal message
	* @global string $this->nick User nickname
	* @global string $this->email User email
	* @global string $this->personal_message User personal message
	* @global string $this->id User id (md5 sum of email and personal message)
	* @return void
	* @access public
	*/
	public function params($nick = null, $email = null, $personal_message = null) {
		if($nick == null || $email == null || $personal_message == null) {
			$this->set_error("invalid params");
		}
		$this->nick = $nick;
		$this->email = $email;
		$this->personal_message = $personal_message;
		$this->id = md5($email . $personal_message);
	}
	
	/**
	* Set user status
	*
	* @see chat::params() Params()
	* @see chat::set_error() Set_error()
	* @see bg_process::run() Bg_process -> run()
	* @param string $status User status
	* @return bool Executed
	* @access public
	*/
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
	
	/**
	* Connect user to chat
	*
	* @see chat::params() Params()
	* @see chat::set_status() Set_status()
	* @see bg_process::run() Bg_process -> run()
	* @param string $status User status
	* @return void Ajax response message
	* @access public
	*/
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
	
	/**
	* Disconnect user to chat
	*
	* @see chat::params() Params()
	* @see chat::set_status() Set_status()
	* @return void Ajax response message
	* @access public
	*/
	public function disconnect() {
		$kill = shell_exec('ps aux | grep -i "' . $this->id . '" | awk {\'print $2\'} | xargs kill -9');
		print "disconnected";
	}
	
	/**
	* Send message
	*
	* @see chat::params() Params()
	* @see chat::proc_exec() Proc_exec()
	* @param string $ip Receiver IP address
	* @param string $message Message to deliver
	* @return void Ajax response message
	* @access public
	*/
	public function send($ip, $message) {
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
			$chat->send("192.168.36.210", $_GET["message"]);
		} else {
			print "no message to send";
		}
		break;
}
*/
?>