<?php
$ip = $_SERVER["SERVER_ADDR"];
	$body = <<<Body
<div style="font-family: Arial, Helvetica; margin-bottom: 15px;"><h1>Oooops... no config.ini file!</h1>
The file config.ini is not been configured.<br />
Please install Ninuxoo Local or <a href="../">launch it</a> one time before...
</div>
Body;
print $body;
?>