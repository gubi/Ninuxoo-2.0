<?php
$setting = parse_ini_file("common/include/conf/general_settings.ini", true);
$usetting = parse_ini_file("common/include/conf/user/" . sha1($username) . "/user.conf", true);
?>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript">
function get_duration(timeSecs){
	var total_time = "";
	if(parseInt(timeSecs.val()) > 0) {
		var str = [],
		units = [
			{label:"seconds",   mod:60},
			{label:"minutes",   mod:60},
			{label:"hours",	 mod:24},
			{label:"days",	  mod:7},
			{label:"weeks",	 mod:52}
		],
		duration = new Object(),
		x = timeSecs.val();
		
		for (i = 0; i < units.length; i++){
			var tmp = x % units[i].mod;
			duration[units[i].label] = Math.round(tmp);
			x = (x - tmp) / units[i].mod;
		}
		if(duration.weeks > 0) {
			str.push(duration.weeks + " settiman" + ((duration.weeks == 1) ? "a" : "e"));
		}
		if(duration.days > 0) {
			str.push(duration.days + " giorn" + ((duration.days == 1) ? "o" : "i"));
		}
		if(duration.hours > 0) {
			str.push(duration.hours + " or" + ((duration.hours == 1) ? "a" : "e"));
		}
		if(duration.minutes > 0) {
			str.push(duration.minutes + " minut" + ((duration.minutes == 1) ? "o" : "i"));
		}
		if(duration.seconds > 0) {
			str.push(duration.seconds + " second" + ((duration.seconds == 1) ? "o" : "i"));
		}
		
		if(str.length > 0) {
			var tott = "";
			for(var i = 0; i < str.length; i++) {
				if(i == 0) {
					tott = str[i];
				} else {
					if(i < (str.length - 1)) {
						tott += ", " + str[i];
					} else {
						tott += " e " + str[i];
					}
				}
			}
		} else {
			tott = str.join(", ");
		}
		total_time =  "second" + ((parseInt(timeSecs.val()) == 1) ? "o" : "i") + " (" + tott + ")";
	} else {
		total_time = "fino alla chiusura della sessione";
	}
	timeSecs.val(Math.round(timeSecs.val()));
	$("#hour").text(total_time);
}

$(document).ready(function() {
	$("#session_length").on("keyup change", function() {
		get_duration($(this));
	});
	$("#session_length").val("<?php print $setting["login"]["session_length"]; ?>");
	
	get_duration($("#session_length"));
	if(window.location.hash) {
		var hash = window.location.hash.substring(1).replace(/\s+/g, "_"),
		target = $("#" + hash).offset().top;
	} else {
		var target = $("h1").eq(1).offset().top;
	}
	$("html, body").animate({ scrollTop: target }, 300);
	
	$("#save_editor_btn").click(function() {
		$("#page_loader").fadeIn(300);
		var password = makeid();
		$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
			var encryptedString = $.jCryption.encrypt($("#settings_frm").serialize(), password);
			
			$.ajax({
				url: "common/include/funcs/_ajax/decrypt.php",
				dataType: "json",
				type: "POST",
				data: {
					jCryption: encryptedString,
					type: "save_settings"
				},
				success: function(response) {
					if (response["data"] !== "ok") {
						var risp = response["data"].split("::");
						if(risp[0] == "error") {
							alert("Si &egrave; verificato un errore durante l'installazione:\n" + risp[1], {icon: "error", title: "Ouch!"});
						}
					} else {
						window.location.href = "./Admin";
					}
				}
			});
		}, function() {
			$("#page_loader").fadeOut(300);
			alert("Si &egrave; verificato un errore durante il salvataggio.", {icon: "error", title: "Ouch!"});
		});
		return false;
	});
});
</script>
<h1>Impostazioni generali</h1>
<br />
<br />
<form method="post" action="" id="settings_frm" onsubmit="false;">
	<fieldset class="frm">
		<legend>Accesso al Sistema <a name="Accesso_al_Sistema" id="Accesso_al_Sistema"></a></legend>
		<label for="session_length">Durata generale della sessione</label>
		<input type="number" size="5" maxlength="7" min="0" step="10" id="session_length" name="session_length" value="" autofocus tabindex="1" />&nbsp;&nbsp;<span id="hour"></span>
	</fieldset>
	<fieldset id="user_management">
		<legend>Gestione utenti <a name="Gestione_utenti" id="Gestione_utenti"></a></legend>
		
		<span class="left">
			<input type="checkbox" <?php print ($setting["login"]["allow_user_registration"] ? "checked" : "") ?> id="allow_user_registration" name="allow_user_registration" tabindex="2" />
			<label for="allow_user_registration">Consenti agli utenti di potersi auto-registrare</label>
		</span>
	</fieldset>
	<fieldset class="frm">
		<legend>Editor degli script <a name="Editor_degli_script" id="Editor_degli_script"></a></legend>
		<p>
			L'editor di testo &egrave; un utile strumento per la lettura e la modifica di linguaggi di programmazione.<br />
			Questo tool appartiene all'editor delle configurazioni dei device, ma &egrave; possibile attivarlo anche in tutto il pannello di amministrazione.
		</p>
		<p>
			Durante il suo uso, &egrave; possibile utilizzare le scorciatoie di tastiera per abilitare le sue funzionalit&agrave; aggiuntive.<br />
			Trascinando un file di testo all'interno dell'editor ne verr&agrave; acquisito il relativo contenuto.<br />
		</p>
		<h3>Scorciatoie da tastiera (attivando il focus nell'editor)</h3>
		<p>
			<b>F11</b>: Attiva la modalit&agrave; schermo intero<br />
			<b>Esc</b>: Esce dalla modalit&agrave; schermo intero<br />
			<b>CTRL+F</b>: Cerca nel testo<br />
			<b>Shift+CTRL+F</b>: Sostituisce un termine nel testo<br />
			<b>CTRL+Invio</b>: Attiva l'autocompletamento<br />
			<b>CTRL+S</b>: Salva il file<br />
			<b>CTRL+D</b>: Attiva il prompt per il download del file
		</p>
		<hr />
		<span class="left">
			<input type="checkbox" <?php print ($usetting["User"]["use_editor_always"] ? "checked" : "") ?> id="allow_editor_always" name="allow_editor_always" tabindex="3" />
			<label for="allow_editor_always">Usa l'editor di linguaggi in tutto il pannello di amministrazione</label>
		</span>
	</fieldset>
	<button id="save_editor_btn">Salva</button>
</form>