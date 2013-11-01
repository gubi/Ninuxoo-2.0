<?php
function parse_conf_file($f, $show_comment = false, $show_line_returns = false){
	$null = "";
	$r = array();
	$first_char = "";
	$sec = $null;
	$comment_chars = ";#";
	$num_comments = "0";
	$num_newline = "0";

	//Read to end of file with the newlines still attached into $f
	$f = @file(trim($f));
	if ($f === false) {
		return -2;
	}
	// Process all lines from 0 to count($f)
	for ($i = 0; $i<@count($f); $i++) {
		$w = @trim($f[$i]);
		$first_char = @substr($w,0,1);
		if ($w) {
			if ((@substr($w,0,1) == "[") and (@substr($w,-1,1)) == "]") {
				$sec = @substr($w,1,@strlen($w)-2);
				$num_comments = 0;
				$num_newline = 0;
			} else if ((stristr($comment_chars, $first_char) == true)) {
				if($show_comment){
					$r[$sec]["Comment_" . $num_comments] = $w;
					$num_comments = $num_comments +1;
				}
			} else {
				// Look for the = char to allow us to split the section into key and value
				$w = @explode(" = ",$w);
				$k = @trim($w[0]);
				unset($w[0]);
				$v = @trim(@implode(" = ",$w));
				// look for the new lines
				if ((@substr($v,0,1) == "\"") and (@substr($v,-1,1) == "\"")) {
					$v = @substr($v,1,@strlen($v)-2);
				}
				$r[$sec][$k] = $v;
			}
		} else {
			if($show_line_returns){
				$r[$sec]["lr_" . $num_newline] = $w;
				$num_newline = $num_newline +1;
			}
		}
	}
	return $r;
}
?>