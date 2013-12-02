<fieldset class="frm">
	<legend>NAS nel vicinato</legend>
	<p>
		A seguire l'elenco dei NAS pi&ugrave; vicini rilevati.
	</p>
	<hr />
	<table cellpadding="10" cellspacing="10">
		<tr>
			<td><strong>Indirizzo IP</strong></td><td><strong>Nome</strong></td><td></td>
		</tr>
		<?php
		$ipss = file_get_contents("n");
		$ips = explode("\n", $ipss);
		foreach(glob("common/include/conf/trusted/*.pem") as $filename) {
			$file[] = $filename;
		}
		if(count($ips) > 0) {
			foreach($ips as $ip) {
				preg_match_all("/.*PTR\t(.*?)\.(.*?)\.nnx\.\n/i", shell_exec('dig -x ' . $ip), $dig);
				if(strlen($dig[1][0]) > 0) {
					$name = ucwords($dig[1][0] . " " . $dig[2][0]);
				} else {
					preg_match_all("/.*\t(.*?)\..*\.nnx/i", shell_exec('dig -x ' . $ip), $dig);
					$name = ucfirst(str_replace("attila", "&emsp;&emsp;&nbsp;&nbsp;~", $dig[1][0]));
				}
				print '<tr><td><a href="./Admin/Config_editor/' . base64_encode($filename) . '">' . $ip . '</a></td><td style="color: #999;">' . $name . '</td><td></td></tr>';
			}
		} else {
			print '<tr><td colspan="2" align="center"><span class="info">Nessuna configurazione personale salvata</span></td></tr>';
		}
		?>
	</table>
</fieldset>