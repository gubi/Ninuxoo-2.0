<?php
header("Content-type: text/plain");

$kill = shell_exec('ps aux | grep -i "' . $output["host"] . '" | awk {\'print $2\'} | xargs kill -9');
print $kill;
?>