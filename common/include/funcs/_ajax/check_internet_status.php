<?php
function check_internet_status() {
	return (trim(shell_exec("ping -q -c 1 8.8.8.8 > /dev/null 2>&1 && echo 1 || echo 0")) == 1) ? "ok" : "disabled";
}
if(isset($_GET["check"]) && trim($_GET["check"]) !== "") {
	header("Content-type: text/plain");
	print check_internet_status();
}
?>