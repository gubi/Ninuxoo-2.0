<?php
class manage_conf_file {
	public $file;
	public $string;
	public $show_comment;
	public $show_line_returns;
	public $search;
	public $replace;
	
	public function parse($file, $is_string = false, $show_comment = false, $show_line_returns = false){
		$null = "";
		$r = array();
		$first_char = "";
		$sec = $null;
		$comment_chars = ";#";
		$num_comments = "0";
		$num_newline = "0";
		
		if(!$is_string) {
			//Read to end of file with the newlines still attached into $f
			$f = @file(trim($file));
			if ($f === false) {
				return -2;
			}
		} else {
			$f = explode("\n", $file);
		}
		// Process all lines from 0 to count($f)
		for ($i = 0; $i < @count($f); $i++) {
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
	
	public function conf_replace($search, $replace, $ini_file) {
		if(require_once(str_replace("//", "/", dirname(__FILE__) . "/") . "../lib/PEAR/File/SearchReplace.php")) {
			if(!class_exists("File_SearchReplace")) {
				print "Error: You need to install PEAR SearchReplace dependancies, type:\npear install File_SearchReplace\n";
				exit();
			}
		}
		$files_to_search = array($ini_file) ;
		$search_string  = "/(" . $search . ").*/";
		if(!is_numeric($replace)){
			$replace = "\"" . $replace . "\"";
		}
		$replace_string = $search . " = " . $replace . "";
		 
		$snr = new File_SearchReplace($search_string,
					      $replace_string,
					      $files_to_search,
					      "", // directorie(s) to search
					      false) ;
		
		$snr->setSearchFunction("preg");
		$snr->doSearch();
		return $snr->getNumOccurences();
	}
}
?>