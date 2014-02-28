<?php
require_once("common/include/lib/getID3-1.9.7/getid3/getid3.php");
require_once("common/include/lib/mime_types.php");

function array_search_key($needle, array $array) {
	$iterator = new RecursiveArrayIterator($array);
	$recursive = new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::SELF_FIRST);
	foreach ($recursive as $key => $value) {
		$res[$key][] = $value;
	}
	if((count($res[$needle])-1) > 1){
		for ($i = 1; $i < count($res[$needle]); $i++) {
			switch($needle) {
				case "sample_rate":	$unity = $res[$needle][$i] . '<small>Hz</small>';	break;
				case "frame_rate":	$unity = $res[$needle][$i] . '<small>fps</small>';	break;
				case "channels":	$unity = $res[$needle][$i] . ((strlen($res["channelmode"][$i]) > 0) ? " (" . $res["channelmode"][$i] . ")" : "");	break;
				case "bitrate":		$unity = abs(round($res[$needle][$i]/1000)) . '<small>Kbps</small>' . ((strlen($res["bitrate_mode"][$i]) > 0) ? " (" . $res["bitrate_mode"][$i] . ")" : "");	break;
				case "resolution_x":	$unity = $res[$needle][$i] . "x" . $res["resolution_y"][$i]; break;
				default:			$unity = $res[$needle][$i]; break;
			}
			$ress[] = "<li>" . $unity . "</li>";
			
		}
		return "<ol>" . @implode("", $ress) . "</ol>";
	} else {
		switch($needle) {
			case "sample_rate":	return $res[$needle][0] . '<small>Hz</small>';	break;
			case "frame_rate":	return $res[$needle][0] . '<small>fps</small>';	break;
			case "channels":	return $res[$needle][0] . ((strlen($res["channelmode"][0]) > 0) ? " (" . $res["channelmode"][0] . ")" : "");	break;
			case "bitrate":
				return abs(round($res[$needle][0]/1000)) . '<small>Kbps</small>';
				break;
			case "resolution_x":	return $res[$needle][0] . "x" . $res["resolution_y"][0]; break;
			default:
				return $res[$needle][0];
				break;
		}
	}
}
function convert_file_size($bytes) {
	$bytes = floatval($bytes);
	$arBytes = array(
		0 => array("unit" => "Tb", "value" => pow(1024, 4)),
		1 => array("unit" => "Gb", "value" => pow(1024, 3)),
		2 => array("unit" => "Mb", "value" => pow(1024, 2)),
		3 => array("unit" => "Kb", "value" => 1024),
		4 => array("unit" => "b", "value" => 1),
	);

	foreach($arBytes as $arItem) {
		if($bytes >= $arItem["value"]) {
			$result = $bytes / $arItem["value"];
			$result = str_replace(".", ",", strval(round($result, 2))) . "<small>" . $arItem["unit"] . "</small>";
			break;
		}
	}
	return $result;
}
function special_escapeshellcmd($string) {
	return str_replace(array("//", " ", "'", "\""), array("/", "\ ", "\'", "\\\""), escapeshellcmd($string));
}
function download_notify($target){
	print '<div class="well text-muted" style="display: none;"><span class="fa fa-caret-down fa-bounce"></span>&nbsp;&nbsp;' . $target . '...&nbsp;&nbsp;<span class="fa fa-spinner fa-spin"></span></div>';
}
function array_combine2($arr1, $arr2) {
	$count = min(count($arr1), count($arr2));
	return array_combine(array_slice($arr1, 0, $count), array_slice($arr2, 0, $count));
}

$h = parse_url($_SERVER["REQUEST_URI"]);
$dhash = $h["query"];
$dact = str_replace(array("/", ":"), "", $h["path"]);

$locale = "it_IT.UTF-8";
setlocale(LC_ALL, $locale);
putenv("LC_ALL=" . $locale);
$file_permission = trim(@shell_exec("stat -c %a " . special_escapeshellcmd($file) . " 2>&1"));
$file_creation = ($file_creation !== "-") ? date("d M Y\&\e\m\s\p\;H:i:s", strtotime($file_creation)) : "-";
$file_last_edit = date("d M Y\&\e\m\s\p\;H:i:s", strtotime(trim(@shell_exec("stat -c %y " . special_escapeshellcmd($file) . " | cut -d'.' -f1 2>&1"))));
$file_last_change = date("d M Y\&\e\m\s\p\;H:i:s", strtotime(trim(@shell_exec("stat -c %z " . special_escapeshellcmd($file) . " | cut -d'.' -f1 2>&1"))));
$file_size = convert_file_size(trim(@shell_exec("stat -c %s " . special_escapeshellcmd($file) . " 2>&1")));

$getID3 = new getID3;
$fileinfo = $getID3->analyze($file);

$file_data["length"] = $fileinfo["playtime_string"];
$file_data["info"] = $fileinfo["tags_html"];
//print "<br />";
if(array_key_exists("id3v1", $fileinfo)) {
	if(array_key_exists("id3v2", $fileinfo)) {
		if(count($fileinfo["id3v2"]) - count($fileinfo["id3v1"]) > 0) {
			$id3v = "id3v1";
		} else {
			$id3v = "id3v2";
		}
	} else {
		$id3v = "id3v1";
	}
} else if(array_key_exists("id3v2", $fileinfo)) {
	$id3v = "id3v2";
}
foreach($fileinfo as $k => $v) {
	/*
	print "<b>" . $k . "</b> - ";
	print_r($v);
	print  "<br />";
	*/
}
//print_r($fileinfo);
//print $id3v . "<br ><br />";
//print_r($file_data);
switch(strtolower($mime_type[strtolower($info["extension"])]["type"])){
	case "ebook":
		switch(strtolower($info["extension"])) {
			case "mobi":
				require_once("common/include/lib/mobi_header.php");
				
				$mobi = new mobi($file);
				$ebook_title = (strlen($mobi->Title()) > 0) ? $mobi->Title() : "";
				$ebook_author = (strlen($mobi->Author()) > 0) ? $mobi->Author() : "";
				$ebook_isbn = (strlen($mobi->Isbn()) > 0) ? $mobi->Isbn() : "";
				if(strlen($mobi->Subject()) > 0) {
					if(strpos($mobi->Subject(), ";") !== false) {
						$ebook_tags = '<span class="label label-warning">' . implode('</span> <span class="label label-warning">', array_map("trim", explode(";", $mobi->Subject()))) . '</span>';
					} elseif(strpos($mobi->Subject(), ",") !== false) {
						$ebook_tags = '<span class="label label-warning">' . implode('</span> <span class="label label-warning">', array_map("trim", explode(",", $mobi->Subject()))) . '</span>';
					}
				} else {
					$ebook_tags = "";
				}
				$ebook_publisher = (strlen($mobi->Publisher()) > 0) ? $mobi->Publisher() : "";
				break;
			case "epub":
				$einfo = trim(shell_exec("einfo " . special_escapeshellcmd($file) . " 2>&1"));
				preg_match_all("/(.*?)(\(s\)|)\:\s([\saut\:]\s.*?\s|.*)\s/i", $einfo, $matched);
				foreach($matched[1] as $mk => $mv) {
					$m[strtolower($mv)] = $matched[3][$mk];
				}
				
				$ebook_title = (strlen($m["title"]) > 0) ? trim($m["title"]) : "";
				$ebook_author = (strlen($m["creator"]) > 0) ? str_replace("aut: ", "", trim($m["creator"])) : "";
				
				break;
			case "pdf":
				$einfo = trim(shell_exec("pdfinfo " . special_escapeshellcmd($file) . " 2>&1"));
				preg_match_all("/(.*?)\:(.*?)\n/i", $einfo, $matched);
				foreach($matched[1] as $mk => $mv) {
					$m[strtolower($mv)] = trim($matched[2][$mk]);
				}
				$ebook_title = $m["title"];
				$ebook_author = $m["author"];
				$ebook_creation_date = (strlen($m["creationdate"]) > 0) ? date("d/m/Y H:i:s", strtotime($m["creationdate"])) : "";
				$ebook_edit_date = (strlen($m["moddate"]) > 0) ? date("d/m/Y H:i:s", strtotime($m["moddate"])) : "";
				$ebook_program = $m["producer"];
				$ebook_tags = ($m["tagged"] == "yes") ? ((strlen($m["keywords"]) > 0) ? '<span class="label label-warning">' . implode('</span> <span class="label label-warning">', array_map("trim", explode(",", $m["keywords"]))) . '</span>' : "") : "";
				$ebook_pages = ($m["pages"] == "0") ? "1" : $m["pages"];
				$ebook_encrypted = $m["encrypted"];
				$ebook_size = $m["page size"];
				$ebook_optimized = ($m["optimized"] == "yes") ? "si" : $m["optimized"];
				
				break;
		}
		break;
	case "image":
		$file_data["file"]["image"] = $fileinfo["video"];
		
		$image_format = (strlen(array_search_key("dataformat", $file_data["file"]["image"])) > 0) ? array_search_key("dataformat", $file_data["file"]["image"]) : "";
		$image_resolution = (strlen(array_search_key("resolution_x", $file_data["file"]["image"])) > 0 && strlen(array_search_key("resolution_y", $file_data["file"]["image"])) > 0) ? array_search_key("resolution_x", $file_data["file"]["image"]) : "";
		break;
	case "audio":
		$keys = explode("_", $GLOBALS["general_settings"]["file data"]["scan_audio_name_order"]);
		$values = preg_split("/[^\w\d\s]+/u", $filename);
		$a = array_combine2($keys, $values);
		$file_data["file"]["audio"] = $fileinfo["audio"];
		$file_data["file"]["tags"] = $fileinfo["tags"][$id3v];
		foreach($a as $ak => $av) {
			$fileinfo["audio"][$ak] = trim($av);
		}
		if(is_array($file_data["file"]["tags"])) {
			$audio_title = (strlen($file_data["file"]["tags"]["title"][0]) > 0) ? $file_data["file"]["tags"]["title"][0] : "";
			$audio_artist = (strlen($file_data["file"]["tags"]["artist"][0]) > 0) ? $file_data["file"]["tags"]["artist"][0] : "";
			$audio_album = (strlen($file_data["file"]["tags"]["album"][0]) > 0) ? $file_data["file"]["tags"]["album"][0] : "";
			$audio_year = ((strlen($file_data["file"]["tags"]["year"][0]) > 0) ? $file_data["file"]["tags"]["year"][0] : $file_data["file"]["tags"]["recording_time"]);
			$audio_track = $file_data["file"]["tags"]["track_number"][0];
			$audio_genre = (is_array($file_data["file"]["tags"]["genre"]) ? implode(", ", $file_data["file"]["tags"]["genre"]) : "");
			$audio_comments = (is_array($file_data["file"]["tags"]["comment"]) ? implode(", ", $file_data["file"]["tags"]["comment"]) : "");
		}
		$audio_length = htmlentities($file_data["length"]);
		
		$audio_format = (strlen(array_search_key("dataformat", $file_data["file"]["audio"])) > 0) ? $file_data["file"]["audio"]["dataformat"] : "";
		$audio_channel = (strlen(array_search_key("channels", $file_data["file"]["audio"])) > 0) ? array_search_key("channels", $file_data["file"]["audio"]) : "";
		$audio_frequency = (strlen(array_search_key("sample_rate", $file_data["file"]["audio"])) > 0) ? array_search_key("sample_rate", $file_data["file"]["audio"]) : "";
		$audio_bitrate = ((strlen(array_search_key("bitrate", $file_data["file"]["audio"])) > 0) ? array_search_key("bitrate", $file_data["file"]["audio"]) : "");
		break;
	case "video":
		$keys = explode("_", $GLOBALS["general_settings"]["file data"]["scan_video_name_order"]);
		$values = preg_split("/[^\w\d\s\'\,\.]+/u", $filename);
		$a = array_combine2($keys, $values);
		foreach($a as $ak => $av) {
			$fileinfo["video"][$ak] = trim($av);
		}
		$file_data["file"]["audio"] = $fileinfo["audio"];
		$file_data["file"]["video"] = $fileinfo["video"];
		
		foreach($file_data["file"] as $k => $v) {
			if($k == "audio") {
				$audio_title = $file_data["info"][$k][$id3v]["title"][0];
				$audio_artist = $file_data["info"][$k][$id3v]["artist"][0];
				$audio_album = $file_data["info"][$k][$id3v]["album"][0];
				$audio_year = ((strlen($file_data["info"][$k][$id3v]["year"][0]) > 0) ? $file_data["info"][$k][$id3v]["year"][0] : $file_data["info"][$k][$id3v]["recording_time"]);
				$audio_track = $file_data["info"][$k][$id3v]["track_number"][0];
				$audio_genre = (is_array($file_data["info"][$k][$id3v]["genre"]) ? implode(", ", $file_data["info"][$k][$id3v]["genre"]) : "");
				$audio_comments = (is_array($file_data["info"][$k][$id3v]["comment"]) ? implode(", ", $file_data["info"][$k][$id3v]["comment"]) : "");
				
				$audio_language = (strlen(array_search_key("language", $file_data["file"]["audio"])) > 0) ? array_search_key("language", $file_data["file"]["audio"]) : "";
				$audio_format = (strlen(array_search_key("dataformat", $file_data["file"]["audio"])) > 0) ? array_search_key("dataformat", $file_data["file"]["audio"]) : "";
				$audio_channel = (strlen(array_search_key("channels", $file_data["file"]["audio"])) > 0) ? array_search_key("channels", $file_data["file"]["audio"]) : "";
				$audio_frequency = (strlen(array_search_key("sample_rate", $file_data["file"]["audio"])) > 0) ? array_search_key("sample_rate", $file_data["file"]["audio"]) : "";
				$audio_bitrate = ((strlen(array_search_key("bitrate", $file_data["file"]["audio"])) > 0) ? array_search_key("bitrate", $file_data["file"]["audio"]) : "");
			}
			if($k == "video") {
				$video_length = (strlen($file_data["length"]) > 0) ? htmlentities($file_data["length"]) : $fileinfo["playtime_string"];
				$video_title = (strlen(array_search_key("title", $file_data["file"]["video"])) > 0) ? array_search_key("title", $file_data["file"]["video"]) : "";
				$video_year = (strlen(array_search_key("year", $file_data["file"]["video"])) > 0) ? array_search_key("year", $file_data["file"]["video"]) : "";
				$video_director = (strlen(array_search_key("director", $file_data["file"]["video"])) > 0) ? array_search_key("director", $file_data["file"]["video"]) : "";
				$video_format = (strlen(array_search_key("dataformat", $file_data["file"]["video"])) > 0) ? array_search_key("dataformat", $file_data["file"]["video"]) : "";
				$video_channel = (strlen(array_search_key("channels", $file_data["file"]["video"])) > 0) ? array_search_key("channels", $file_data["file"]["video"]) : "";
				$video_codec = (strlen(array_search_key("codec", $file_data["file"]["video"])) > 0) ? array_search_key("codec", $file_data["file"]["video"]) : "";
				$video_frames = (strlen(array_search_key("frame_rate", $file_data["file"]["video"])) > 0) ? array_search_key("frame_rate", $file_data["file"]["video"]) : "";
				$video_tot_frames = (strlen(array_search_key("total_frames", $file_data["file"]["video"])) > 0) ? array_search_key("total_frames", $file_data["file"]["video"]) : "";
				$video_resolution = (strlen(array_search_key("resolution_x", $file_data["file"]["video"])) > 0 && strlen(array_search_key("resolution_y", $file_data["file"]["video"])) > 0) ? array_search_key("resolution_x", $file_data["file"]["video"]) : "";
				$video_bitrate = (strlen(array_search_key("bitrate", $file_data["file"]["video"])) > 0) ? array_search_key("bitrate", $file_data["file"]["video"]) : "";
			}
		}
		break;
}
?>
<link rel="stylesheet" href="common/js/jquery.treeview/jquery.treeview.css" type="text/css" media="screen" />
<script type="text/javascript" src="common/js/jquery.treeview/jquery.treeview.js"></script>
<script type="text/javascript" src="common/js/jquery.highlight-4.js"></script>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/bootstrap-fa-star-rating.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript" src="common/js/include/local_search.js"></script>

<span style="display: none;" id="result_type">View</span>
<span style="display: none;" id="result_filetype"><?php print strtolower($mime_type[strtolower($info["extension"])]["type"]); ?></span>
<span style="display: none;" id="result_owl"><?php print $mime_type[strtolower($info["extension"])]["owl"]; ?></span>
<span style="display: none;" id="result_semantic"><?php print $mime_type[strtolower($info["extension"])]["semantic"]; ?></span>
<span style="display: none;" id="search_type"><?php print $GLOBALS["search_type"]; ?></span>
<span style="display: none;" id="search_num_results"><?php print $GLOBALS["search_num_results"]; ?></span>
<span style="display: none;" id="search_ip"><?php print $GLOBALS["search_ip"]; ?></span>
<span style="display: none;" id="search_filetype"><?php print $GLOBALS["search_filetype"]; ?></span>
<span style="display: none;" id="hash"><?php print $dhash; ?></span>
<div id="result_content">
	<div class="row">
		<div class="panel right col-lg-4" id="affix">
			<div class="panel list-group">
				<a class="list-group-item active" style="text-decoration: none;" href="<?php print (isset($_COOKIE["n"]) ? "./Scarica:?" . $dhash : "javascript:void(0);"); ?>">
					<span class="right lead" style="font-weight: bold; opacity: 0.5;"><?php print $file_size; ?></span>
					<h4 class="list-group-item-heading"><span class="fa fa-cloud-download"></span>&nbsp;&nbsp;Scarica</h4>
					<p class="list-group-item-text">
						<strong><?php print $filename; ?></strong><br />
						<small>File <span id="file_ext"><?php print strtoupper($info["extension"]); ?></span> (<span id="file_mime"><?php print $mime_type[strtolower($info["extension"])]["mime"]; ?></span>)</small>
					</p>
					<?php print (!isset($_COOKIE["n"]) ? '<div class="text-center"><br /><small><strong>NOTA: per scaricare questo file devi effettuare l\'accesso</strong></small> <span class="fa fa-sign-in"></span></div>' : ""); ?>
				</a>
			</div>
			<div class="panel">
				<big class="lead text-primary"><span class="fa fa-tasks"></span>&nbsp;&nbsp;STATISTICHE</big>
			</div>
			<div class="panel">
				<p class="lead media-heading text-muted"><span class="fa fa-file"></span>&nbsp;&nbsp;File</p>
				<dl class="dl-horizontal">
					<dt>Permessi:</dt><dd id="file_permissions"><?php print $file_permission; ?></dd>
					<dt>Modificato il:</dt><dd id="file_last_edit"><?php print $file_last_edit; ?></dd>
					<dt>Aggiornato il:</dt><dd id="file_last_change"><?php print $file_last_change; ?></dd>
					<dt>Peso:</dt><dd id="file_size"><?php print $file_size; ?></dd>
				</dl>
				<dl class="dl-horizontal">
					<dt>Estensione:</dt><dd id="file_extension">.<?php print strtolower($info["extension"]); ?></dd>
					<dt>MIME:</dt><dd id="file_mime"><?php print $mime_type[strtolower($info["extension"])]["mime"]; ?></dd>
					<dt>Categoria:</dt><dd id="file_category"><?php print $mime_type[strtolower($info["extension"])]["text"]; ?></dd>
				</dl>
				<br />
				<?php
				switch(strtolower($mime_type[strtolower($info["extension"])]["type"])){
					case "text":
						break;
					case "ebook":
						?>
						<p class="lead media-heading text-muted"><span class="fa fa-book"></span>&nbsp;&nbsp;E-book</p>
						<dl class="dl-horizontal">
							<?php (strlen($ebook_title) > 0) ? print '<dt>Titolo:</dt><dd id="ebook_title">' . $ebook_title . '</dd>' : ""; ?>
							<?php (strlen($ebook_author) > 0) ? print '<dt>Autore:</dt><dd id="ebook_author">' . $ebook_author . '</dd>' : ""; ?>
							<?php (strlen($ebook_isbn) > 0) ? print '<dt>ISBN:</dt><dd id="ebook_isbn">' . $ebook_isbn . '</dd>' : ""; ?>
							<?php (strlen($ebook_tags) > 0) ? print '<dt>Tags:</dt><dd id="ebook_subject">' . $ebook_tags . '</dd>' : ""; ?>
							<?php (strlen($ebook_publisher) > 0) ? print '<dt>Editore:</dt><dd id="ebook_publisher">' . $ebook_publisher . '</dd>' : ""; ?>
							<?php (strlen($ebook_creation_date) > 0) ? print '<dt>Creato il:</dt><dd id="ebook_creation_date">' . $ebook_creation_date . '</dd>' : ""; ?>
							<?php (strlen($ebook_edit_date) > 0) ? print '<dt>Modificato il:</dt><dd id="ebook_edit_date">' . $ebook_edit_date . '</dd>' : ""; ?>
						</dl>
						<dl class="dl-horizontal">
							<?php (strlen($ebook_pages) > 0) ? print '<dt>Pagine:</dt><dd id="ebook_pages">' . $ebook_pages . '</dd>' : ""; ?>
							<?php (strlen($ebook_size) > 0) ? print '<dt>Dimensione:</dt><dd id="ebook_size">' . $ebook_size . '</dd>' : ""; ?>
							<?php (strlen($ebook_encrypted) > 0) ? print '<dt>Cifrato:</dt><dd id="ebook_encrypted">' . $ebook_encrypted . '</dd>' : ""; ?>
							<?php (strlen($ebook_program) > 0) ? print '<dt>Prodotto con:</dt><dd id="ebook_program">' . $ebook_program . '</dd>' : ""; ?>
							<?php (strlen($ebook_optimized) > 0) ? print '<dt>Ottimizzato (web):</dt><dd id="ebook_optimized">' . $ebook_optimized . '</dd>' : ""; ?>
						</dl>
						<?php
						break;
					case "image":
						?>
						<p class="lead media-heading text-muted"><span class="fa fa-picture-o"></span>&nbsp;&nbsp;Immagine</p>
						<dl class="dl-horizontal">
							<?php (strlen($image_format) > 0) ? print '<dt>Formato:</dt><dd id="media_title">' . $image_format . '</dd>' : ""; ?>
							<?php (strlen($image_resolution) > 0) ? print '<dt>Dimensione:</dt><dd id="media_artist">' . $image_resolution . '</dd>' : ""; ?>
						</dl>
						<?php
						break;
					case "audio":
						?>
						<p class="lead media-heading text-muted"><span class="fa fa-music"></span>&nbsp;&nbsp;Audio</p>
						<dl class="dl-horizontal">
							<?php (strlen($audio_title) > 0) ? print '<dt>Titolo:</dt><dd id="media_title">' . $audio_title . '</dd>' : ""; ?>
							<?php (strlen($audio_artist) > 0) ? print '<dt>Autore:</dt><dd id="media_artist">' . $audio_artist . '</dd>' : ""; ?>
							<?php (strlen($audio_album) > 0) ? print '<dt>Album:</dt><dd id="media_album">' . $audio_album . '</dd>' : "" ?>
							<?php (strlen($audio_year) > 0) ? print '<dt>Anno:</dt><dd id="media_year">' . $audio_year . '</dd>' : ""; ?>
							<?php (strlen($audio_length) > 0) ? print '<dt>Durata:</dt><dd id="media_length">' . $audio_length . '</dd>' : ""; ?>
							<?php (strlen($audio_track) > 0) ? print '<dt>N° Traccia:</dt><dd id="media_track">' . $audio_track . '</dd>' : "" ?>
							<?php (strlen($audio_genre) > 0) ? print '<dt>Genere:</dt><dd id="media_genre">' . $audio_genre . '</dd>' : "" ?>
							<?php (strlen($audio_comments) > 0) ? print '<dt>Commenti:</dt><dd id="media_comments"><i>' . $audio_comments . '</i></dd>' : "" ?>
						</dl>
						<dl class="dl-horizontal">
							<?php (strlen($audio_format) > 0) ? print '<dt>Formato:</dt><dd id="media_format">' . $audio_format . '</dd>' : ""; ?>
							<?php (strlen($audio_frequency) > 0) ? print '<dt>Frequenza:</dt><dd id="media_frequency">' . $audio_frequency . '</dd>' : ""; ?>
							<?php (strlen($audio_channel) > 0) ? print '<dt>Canali:</dt><dd id="media_channel">' . $audio_channel . '</dd>' : ""; ?>
							<?php (strlen($audio_bitrate) > 0) ? print '<dt>Bitrate:</dt><dd id="media_bitrate">' . $audio_bitrate . '</dd>' : ""; ?>	
						</dl>
						<?php
						break;
					case "video":
						?>
						<p class="lead media-heading text-muted"><span class="fa fa-music"></span>&nbsp;&nbsp;Audio</p>
						<dl class="dl-horizontal">
							<?php (strlen($audio_language) > 0) ? print '<dt>Lingua:</dt><dd id="media_format">' . $audio_language . '</dd>' : ""; ?>
							<?php (strlen($audio_format) > 0) ? print '<dt>Formato:</dt><dd id="media_format">' . $audio_format . '</dd>' : ""; ?>
							<?php (strlen($audio_frequency) > 0) ? print '<dt>Frequenza:</dt><dd id="media_frequency">' . $audio_frequency . '</dd>' : ""; ?>
							<?php (strlen($audio_channel) > 0) ? print '<dt>Canali:</dt><dd id="media_channel">' . $audio_channel . '</dd>' : ""; ?>
							<?php (strlen($audio_bitrate) > 0) ? print '<dt>Bitrate:</dt><dd id="media_bitrate">' . $audio_bitrate . '</dd>' : ""; ?>			
						</dl>
						<p class="lead media-heading text-muted"><span class="fa fa-film"></span>&nbsp;&nbsp;Video</p>
						<dl class="dl-horizontal">
							<?php (strlen($video_title) > 0) ? print '<dt>Titolo:</dt><dd id="media_title">' . $video_title . '</dd>' : ""; ?>
							<?php (strlen($video_year) > 0) ? print '<dt>Anno:</dt><dd id="media_year">' . $video_year . '</dd>' : "" ?>
							<?php (strlen($video_director) > 0) ? print '<dt>Regia:</dt><dd id="media_director">' . $video_director . '</dd>' : ""; ?>
						</dl>
						<dl class="dl-horizontal">
							<?php (strlen($video_length) > 0) ? print '<dt>Durata:</dt><dd id="media_length">' . $video_length . '</dd>' : ""; ?>
							<?php (strlen($video_format) > 0) ? print '<dt>Formato:</dt><dd id="video_format">' . $video_format . '</dd>' : ""; ?>
							<?php (strlen($video_channel) > 0) ? print '<dt>Canali:</dt><dd id="video_channel">' . $video_channel . '</dd>' : ""; ?>
							<?php (strlen($video_codec) > 0) ? print '<dt>Codifica:</dt><dd id="video_codec">' . $video_codec . '</dd>' : ""; ?>
							<?php (strlen($video_resolution) > 0) ? print '<dt>Dimensione:</dt><dd id="video_resolution">' . $video_resolution . '</dd>' : ""; ?>
							<?php (strlen($video_frequency) > 0) ? print '<dt>Frequenza:</dt><dd id="video_frequency">' . $video_frequency . '</dd>' : ""; ?>
							<?php (strlen($video_frames) > 0) ? print '<dt>Framerate:</dt><dd id="video_frames">' . $video_frames . '</dd>' : ""; ?>
							<?php (strlen($video_tot_frames) > 0) ? print '<dt>Fotogrammi totali:</dt><dd id="video_tot_frames">' . $video_tot_frames . '</dd>' : ""; ?>
							<?php (strlen($video_bitrate) > 0) ? print '<dt>Bitrate:</dt><dd id="video_bitrate">' . $video_bitrate . '</dd>' : ""; ?>			
						</dl>
						<?php
						break;
				}
				?>
				<hr />
				<p class="lead media-heading text-muted"><span class="picol picol_combine"></span>&nbsp;&nbsp;Files con lo stesso nome</p>
				<dl class="dl-horizontal">
					<dt>Termini ricercati:</dt><dd id="nlabels"><span class="fa fa-refresh fa-spin text-muted"></span></dd>
					<dt>Numero di risultati:</dt><dd id="nresults"><span class="fa fa-refresh fa-spin text-muted"></span></dd>
					<dt>Durata della ricerca:</dt><dd id="searchtime"><span class="fa fa-refresh fa-spin text-muted"></span></dd>
				</dl>
				<hr />
			</div>
		</div>
		<div class="panel col-lg-8">
			<div class="panel">
				<?php require_once("common/tpl/share_btns.tpl"); ?>
				<span class="lead text-primary">
					<span class="fa fa-file-o"></span>&nbsp;&nbsp;<span id="search_term"><?php print $filename; ?></span>
					<small class="help-block">Scheda del file <?php require_once("common/tpl/rating.tpl"); ?></small>
				</span>
			</div>
			<div class="panel" id="search_view">
				<?php
				switch(strtolower($mime_type[strtolower($info["extension"])]["type"])){
					case "text":
						?>
						<iframe src="./Scarica:?view=true&<?php print $dhash; ?>" style="width: 100%; height: 50em; border: #ccc 1px solid; padding: 1em;"></iframe>
						<?php
						break;
					case "image":
						require_once("common/tpl/image_gallery.tpl");
						?>
						<div id="links" style="background-color: #333; padding: 2em 0;">
							<a href="./Scarica:?view=true&<?php print $dhash; ?>" title="<?php print $filename; ?>" data-gallery>
								<img src="./Scarica:?view=true&<?php print $dhash; ?>" class="img-responsive" style="margin: 0 auto;" alt="Immagine: <?php print $filename; ?>">
							</a>
						</div>
						<?php
						break;
					case "audio":
						?>
						<div class="well text-muted" style="display: none;"><span class="fa fa-caret-right"></span>&nbsp;&nbsp;Analizzo lo spettro del campione audio...&nbsp;&nbsp;<span class="fa fa-magic fa-bounce"></span></div>
						<div id="audio_spectrum"></div>
						<?php
						break;
					case "ebook":
						if(strtolower($info["extension"]) == "pdf") {
							?>
							<iframe src="./Scarica:?view=true&<?php print $dhash; ?>" style="width: 100%; height: 50em; border: 0px none;"></iframe>
							<?php
						}
						break;
				}
				?>
			</div>
		</div>
		<?php download_notify("Acquisisco dati semantici sul brano"); ?>
		<div class="panel col-lg-8" id="semantic_track" style="display: none;">
			<div class="panel">
				<span class="lead text-primary">
					<span class="fa fa-list-alt"></span>&nbsp;&nbsp;<span id="track_name"></span><small class="help-block">Risultati semantici relativi al brano</small>
				</span>
			</div>
		</div>
		<?php download_notify("Acquisisco dati semantici sull'album"); ?>
		<div class="panel col-lg-8" id="semantic_album" style="display: none;">
			<div class="panel">
				<span class="lead text-primary">
					<span class="fa fa-list-alt"></span>&nbsp;&nbsp;<span id="album_name"></span><small class="help-block">Risultati semantici relativi all'album</small>
				</span>
			</div>
		</div>
		<?php download_notify("Acquisisco dati semantici sull'artista"); ?>
		<div class="panel col-lg-8" id="semantic_artist" style="display: none;">
			<div class="panel">
				<span class="lead text-primary">
					<span class="fa fa-microphone"></span>&nbsp;&nbsp;<span id="artist_name"></span><small class="help-block">Risultati semantici relativi all'artista o il complesso</small>
				</span>
			</div>
		</div>
		<?php download_notify("Acquisisco dati semantici sul film"); ?>
		<div class="panel col-lg-8" id="semantic_film" style="display: none;">
			<div class="panel">
				<span class="lead text-primary">
					<span class="fa fa-film"></span>&nbsp;&nbsp;<span id="film_name"><?php (strlen($video_title) > 0) ? print $video_title : ""; ?></span><small class="help-block">Risultati semantici relativi al film</small>
				</span>
			</div>
		</div>
		<?php download_notify("Acquisisco dati semantici sulla regia"); ?>
		<div class="panel col-lg-8" id="semantic_director" style="display: none;">
			<div class="panel">
				<span class="lead text-primary">
					<span class="fa fa-video-camera"></span>&nbsp;&nbsp;<span id="director_name">Regia</span><small class="help-block">Risultati semantici relativi alla regia</small>
				</span>
			</div>
		</div>
	</div>
	<?php download_notify("Acquisisco dati semantici sul file"); ?>
	<div class="row" id="semantic_info" style="display: none;" class="panel">
		<div class="panel col-lg-4 right">
			<div class="panel">
				<p class="lead text-muted"><span class="picol picol_semantic_web"></span>&nbsp;&nbsp;Contesto semantico</p>
				<dl class="dl-horizontal">
					<dt><span class="picol picol_owl_lite"></span>&nbsp;&nbsp;OWL:</dt><dd id="nlabels"><a target="_blank" href="http://dbpedia.org/ontology/<?php print $mime_type[strtolower($info["extension"])]["owl"]; ?>"><?php print $mime_type[strtolower($info["extension"])]["owl"]; ?></a></dd>
				</dl>
				<div id="file_info"></div>
			</div>
		</div>
		<div class="panel panel-default col-lg-8">
			<div class="panel media-heading">
				<span class="lead text-primary">
					<span class="picol picol_rdf"></span>&nbsp;&nbsp;<span id="s_label"><?php print $mime_type[strtolower($info["extension"])]["semantic"]; ?></span> <small class="help-block">Risultati semantici relativi al tipo di file</small>
				</span>
			</div>
			<div id="semantic_results" class="panel-body"></div>
		</div>
	</div>
	<hr />
	<div class="row">
		<div class="panel col-lg-12">
			<div class="panel">
				<span class="lead text-primary">
					<span class="fa fa-search"></span>&nbsp;&nbsp;Duplicati: <small class="help-block">Percorsi in cui vi &egrave; un file con lo stesso nome</small>
				</span>
			</div>
			<div class="panel" id="search_results"><?php print $content; ?></div>
		</div>
	</div>
</div>