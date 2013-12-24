<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/setup.js"></script>
<?php
$setting = parse_ini_file("common/include/conf/general_settings.ini", true);
if(!isset($_GET["q"]) || trim($_GET["q"]) !== "Password_dimenticata") {
	?>
	<script type="text/javascript" src="common/js/include/login.js"></script>
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
							<label for="username">Indirizzo e-mail:</label>
							<input type="email" style="width: 50%;" id="username" name="username" value="" placeholder="Indirizzo e-mail" autocomplete="off" tabindex="1" />
							
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
	if(isset($_GET["q"]) && trim($_GET["q"]) == "Password_dimenticata") {
		if(!isset($_GET["id"])) {
			?>
			<script type="text/javascript" src="common/js/include/reset_password.send_mail.js"></script>
			<form method="post" action="" class="frm" id="reset_pwd_frm" onsubmit="return false;">
				<fieldset>
					<legend>Reset della password</legend>
					
					<label for="username">Indirizzo e-mail: <input type="email" style="width: 50%;" id="username" name="username" value="" placeholder="Indirizzo e-mail" autocomplete="off" tabindex="1" /></label>
				</fieldset>
				<button id="reset_btn" tabindex="2">Prosegui</button>
			</form>
			<?php
		} else {
			$hash = explode("::::", urldecode(base64_decode($_GET["id"])));
			if($hash[1] == sha1(date("d-m-Y"))) {
				?>
				<script type="text/javascript" src="common/js/include/reset_password.js"></script>
				<form method="post" action="" class="frm" id="reset_pwd_frm" onsubmit="return false;">
					<fieldset>
						<legend>Imposta una nuova password</legend>
						
						<input type="hidden" name="username" value="<?php print $hash[0]; ?>" />
						<label for="password">Password:</label>
						<input type="password" id="password" name="password" value="" placeholder="Una password valida" autocomplete="off" tabindex="1" />
						
						<label for="password">Password:</label>
						<input type="password" id="password2" name="password2" value="" placeholder="Ripeti password" autocomplete="off" tabindex="2" />
					</fieldset>
					<button id="reset_btn" tabindex="3">Resetta</button>
				</form>
				<?php
			} else {
				?>
				<h1>Oops...</h1>
				<h2>Tempo scaduto!</h2>
				<p>Il tempo concesso per questo link &egrave; scaduto e non &egrave; pi&ugrave; possibile procedere con questa istanza.<br />Riprova con una <a href="./Accedi/Password_dimenticata/">nuova sessione del processo</a></p>
				<?php
			}
		}
	}
}
?>