<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script src="common/js/include/common.js"></script>
<script src="common/js/include/linked_nas.js"></script>
<fieldset class="frm">
	<legend>NAS nel vicinato</legend>
	<p>
		A seguire l'elenco dei NAS nelle vicinanze.<br />
		Per attivare o meno il collegamento con uno dei quali fare click sul flag corrispondente nella colonna dello stato .
	</p>
	<hr />
	<table cellpadding="10" cellspacing="10">
		<tr>
			<td></td>
			<td><strong>Nome</strong></td>
			<td><strong>Proprietario</strong></td>
			<td><strong>Zona</strong></td>
			<td><strong>Raggiungibilit&agrave;</strong></td>
			<td class="mark_btns"><strong>Stato</strong><table cellpadding="0" cellspacing="0"><tr><td style="width: 50%;"><small>Trusted</small></td><td><small>Untrusted</small></td></tr></table></td>
		</tr>
		<?php
		require_once("common/include/classes/mdns.class.php");
		
		$mdns = new mdns();
		$ndata = $mdns->scan(true);
		print_r($ndata);
		if (is_array($ndata)) {
			foreach($ndata as $hostname => $ip) {
				switch($ip["status"]) {
					case "trusted":
						$img = '<img src="common/media/img/mainframe_accept_32_333.png" />';
						$status_t = "trusted selected";
						$status_u = "untrusted";
						break;
					case "untrusted":
						$img = '<img src="common/media/img/mainframe_cancel_32_333.png" />';
						$status_t = "trusted";
						$status_u = "untrusted selected";
						break;
					case "unknown":
					default:
						$img = '<img src="common/media/img/mainframe_run_32_333.png" />';
						$status_t = "trusted";
						$status_u = "untrusted";
						break;
				}
				sort($ip["reachability"]);
				print '<tr><td class="status">' . $img . '</td><td class="hostname"><i>' . $hostname . '</i><input type="hidden" class="token" value="' . $ip["token"] . '" /></td><td class="owner"><a title="Contatta il proprietario di questo NAS" href="mailto:' . $ip["owner"]["email"] . '">0x' . $ip["owner"]["key"] . '</a></td><td style="color: #999;">' . trim($ip["geo_zone"], '"') . '</td><td style="color: #999;">' . implode(", ", $ip["reachability"]) . '</td><td class="mark_btns" style="color: #999;"><table cellpadding="0" cellspacing="0" class="mark"><tr><td class="' . $status_t . '"></td><td class="' . $status_u . '"></td></tr></table></td></tr>';
				print '<tr><td class="status">' . $img . '</td><td class="hostname"><i>' . $hostname . ' 2</i><input type="hidden" class="token" value="' . $ip["token"] . '" /></td><td class="owner"><a title="Contatta il proprietario di questo NAS" href="mailto:' . $ip["owner"]["email"] . '">0x' . $ip["owner"]["key"] . '</a></td><td style="color: #999;">' . trim($ip["geo_zone"], '"') . '</td><td style="color: #999;">' . implode(", ", $ip["reachability"]) . '</td><td class="mark_btns" style="color: #999;"><table cellpadding="0" cellspacing="0" class="mark"><tr><td class="' . $status_t . '"></td><td class="' . $status_u . '"></td></tr></table></td></tr>';
			}
		} else {
			print '<tr><td colspan="6" align="center"><span class="info">Nessuna configurazione personale salvata</span></td></tr>';
		}
		?>
	</table>
</fieldset>