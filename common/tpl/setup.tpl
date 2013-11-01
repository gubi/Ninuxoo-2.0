<script type="text/javascript">
	$(document).ready(function(){
		alert("ok");
	});
</script>
<div id="content">
	<form method="post" action="">
		<fieldset>
			<legend>Nodo</legend>
			<table cellspacing="10" cellpadding="10" style="width: 100%;">
				<tr>
					<th style="width: 15%;">
						<label for="node_name">Nome del nodo</label>
					</th>
					<td>
						<input type="text" name="node_name" id="node_name" style="width: 25%;" autofocus value="" />
					</td>
				</tr>
				<tr>
					<th>
						<label for="node_map">Indirizzo del map-server</label>
					</th>
					<td>
						<input type="url" name="node_map" id="node_map" style="width: 50%;" autofocus value="" placeholder="http://map.ninux.org/select/..." />
					</td>
				</tr>
				<tr>
					<th>
						<label for="node_type">Tipo di nodo</label>
					</th>
					<td>
						<select name="node_type" id="node_type">
							<option selected name="active">Attivo</option>
							<option name="hotspot">HotSpot</option>
							<option name="mesh_active">Attivo con Mesh</option>
							<option name="mesh_hotspot">HotSpot con Mesh</option>
						</select>
					</td>
				</tr>
			</table>
		</fieldset>
		<fieldset>
			<legend><acronym title="Network Attached Storage">NAS</acronym></legend>
			<table cellspacing="10" cellpadding="10" style="width: 100%;">
				<tr>
					<td>
						<label><input type="checkbox" name="remote_nas" id="remote_nas" checked />&emsp;Il NAS &egrave; in una posizione remota ed &egrave; gestito da controller apposito
					</td>
				</tr>
			</table>
			<br />
			<table cellspacing="10" cellpadding="10" style="width: 100%;">
				<tr>
					<th style="width: 22%;">
						<label for="smb_conf_dir">Directory del file di configurazione SAMBA</label>
					</th>
					<td>
						<input type="text" name="smb_conf_dir" id="smb_conf_dir" style="width: 25%;" value="/mnt/NAS/" placeholder="/etc/samba/" />
					</td>
				</tr>
				<tr>
					<th>
						<label for="server_root">Directory root del Server</label>
					</th>
					<td>
						<input type="text" name="server_root" id="server_root" style="width: 50%;" value="/var/www/" placeholder="/var/www/" />
					</td>
				</tr>
				<tr>
					<th>
						<label for="api_dir">Directory per le API</label>
					</th>
					<td>
						<input type="text" name="api_dir" id="api_dir" style="width: 50%;" value="/var/www/API/" placeholder="/var/www/API/" />
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>