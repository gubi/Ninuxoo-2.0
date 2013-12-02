<div style="position: fixed; width: 568px; height: 565px; left: 50%; margin-left: -60px; bottom: 0; background: url('common/media/img/404_pidgeon.png') center bottom no-repeat;"></div>
<div id="content">
	<h1>Ooops...</h1>
	<h2>Questa pagina non esiste</h2>
	<p>
		La pagina che si sta cercando non &egrave; stata trovata.<br />
		<?php
		foreach(glob("common/md/pages/*.md") as $filename) {
			$info = pathinfo($filename);
			similar_text($info["filename"], $_GET["s"], $percent);
			if($percent > 60) {
				$files[$percent] = $info["filename"];
			}
		}
		if(is_array($files)) {
			krsort($files);
			if(count($files) >= 1) {
				if(count($files) == 1) {
					foreach($files as $file) {
						print 'Forse si stava cercando la pagina <a href="./' . $file . '">' . $file . '</a>';
					}
				} else {
					foreach($files as $kf => $file) {
						$link_list .= '<li><a href="./' . $file . '">' . $file . '</a></li>';
					}
					
					print "Forse si stava cercando una di queste pagine:<ul>" . $link_list . "</ul>";
				}
			}
		}
		?>
	</p>
</div>