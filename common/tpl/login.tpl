<?php
$setting = parse_ini_file("common/include/conf/general_settings.ini", true);
if(!isset($_GET["q"]) || trim($_GET["q"]) !== "Password_dimenticata") {
	?>
	<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
	<script type="text/javascript" src="common/js/include/setup.js"></script>
	<script type="text/javascript">
	function login() {
		if($("#username").val() !== "") {
			if($("#password").val() !== "") {
				$("#page_loader").fadeIn(150);
				var password = makeid();	
				$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
					var encryptedString = $.jCryption.encrypt($("#login_frm").serialize(), password);
					
					$.ajax({
						url: "common/include/funcs/_ajax/decrypt.php",
						dataType: "json",
						type: "POST",
						data: {
							jCryption: encryptedString,
							type: "login"
						},
						success: function(result) {
							if(result["error"]) {
								apprise(result["message"], {}, function(r) {
									if(r) {
										$("#username").addClass("error").focus();
										$("#password").addClass("error");
										$("#page_loader").fadeOut(150);
									}
								});
							} else {
								$(window.location).attr("href", "./Admin");
							}
						}
					});
				});
			} else {
				$("#password").addClass("error").focus();
			}
		} else {
			$("#username").addClass("error").focus();
		}
		return false;
	}
	$(document).ready(function() {
		$("#username, #password").on("keyup change", function() {
			if($(this).val().length > 0) {
				$(this).removeClass("error");
			} else {
				$(this).addClass("error");
			}
		});
		$("#username").focus();
	});
	</script>
	<div>
		<div id="loader"></div>
		<form method="post" action="" class="frm" id="login_frm" onsubmit="login(); return false;">
			<fieldset>
				<legend>Dati per l'accesso</legend>
				<p>
					Inserire i dati di accesso creati in fase di installazione di Ninuxoo e relativi alla propria chiave di cifratura PGP.<br />
				</p>
				<hr />
				<table cellspacing="5" cellpadding="5" class="login">
					<tr>
						<td>
							<label for="username">Username:</label>
							<input type="email" style="width: 50%;" id="username" name="username" value="" placeholder="Username" autocomplete="off" tabindex="1" />
							
							<label for="password">Password:</label>
							<input type="password" id="password" name="password" value="" placeholder="Una password valida" autocomplete="off" tabindex="2" />
						</td>
						<td class="separator"></td>
						<td>
							<ul>
								<li><a href="./Accedi/Password_dimenticata">Non ricordo pi&ugrave; la password</a></li>
								<?php
								if($setting["login"]["allow_user_registration"] == "true") {
									print '<li><a href="./Registrati">Registrati</a></li>';
								}
								?>
							</ul>
						</td>
					</tr>
				</table>
			</fieldset>
			<button id="login_btn" tabindex="3">Accedi</button>
		</form>
	</div>
<?php
} else {
	print $_GET["q"];
}
?>