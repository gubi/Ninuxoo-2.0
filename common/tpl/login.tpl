<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/setup.js"></script>
<?php
$setting = parse_ini_file("common/include/conf/general_settings.ini", true);
if(!isset($_GET["q"]) || trim($_GET["q"]) !== "Password_dimenticata") {
	?>
	<script type="text/javascript" src="common/js/include/login.js"></script>
	<div>
		<div id="loader"></div>
		<form method="post" action="" class="form-horizontal" id="login_frm" onsubmit="login(); return false;" role="form">
			<h1>Accedi</h1>
			<br />
			<p class="help-block"><span class="glyphicon glyphicon-info-sign"></span>&nbsp;&nbsp;Nota: Gli utenti amministratori dovranno inserire i dati di accesso creati in fase di installazione e relativi alla propria chiave di cifratura <acronym title="Pretty Good Privacy">PGP</acronym>.</p>
			<br />
			<div class="well">
				<div class="input-group">
					<label for="username" class="control-label input-group-addon btn"><span class="glyphicon glyphicon-envelope" title="Indirizzo e-mail"></span></label>
					<input type="email" class="form-control input-lg" id="username" name="username" placeholder="Indirizzo e-mail" autocomplete="off" autofocus required tabindex="1" />
				</div>
				<br />
				<div class="input-group">
					<label for="password" class="control-label input-group-addon btn"><span class="glyphicon glyphicon-lock" title="password"></span></label>
					<input type="password" class="form-control input-lg" id="password" name="password" placeholder="Una password valida" autocomplete="off" required tabindex="2" />
				</div>
				<br />
				<a class="btn btn-link" href="./Accedi/Password_dimenticata"><span class="glyphicon glyphicon-remove"></span> Password dimenticata</a>
			</div>
			<div class="btn-group right">
				<?php
				if($setting["login"]["allow_user_registration"] == "true") {
					print '<a class="btn btn-default" href="./Registrati"><span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;&nbsp;Registrati</a>';
				}
				?>
				<button class="btn btn-primary right" id="login_btn" tabindex="3">Accedi&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-log-in"></span></button>
			</div>
		</form>
	</div>
	<?php
} else {
	if(isset($_GET["q"]) && trim($_GET["q"]) == "Password_dimenticata") {
		if(!isset($_GET["id"])) {
			?>
			<script type="text/javascript" src="common/js/include/reset_password.send_mail.js"></script>
			<form method="post" action="" class="form-horizontal" id="reset_pwd_frm" onsubmit="return false;">
				<h1>Reset della password</h1>
				<br />
				<div class="well">
					<div class="input-group">
						<label for="username" class="control-label input-group-addon btn"><span class="glyphicon glyphicon-envelope" title="Indirizzo e-mail"></span></label>
						<input type="email" class="form-control input-lg" id="username" name="username" placeholder="Indirizzo e-mail" autocomplete="off" tabindex="1" />
					</div>
				</div>
				<button id="reset_btn" class="btn btn-primary right" tabindex="2">Invia&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-send"></span></button>
			</form>
			<?php
		} else {
			$hash = explode("::::", urldecode(base64_decode($_GET["id"])));
			if($hash[1] == sha1(date("d-m-Y"))) {
				?>
				<script type="text/javascript" src="common/js/include/reset_password.js"></script>
				<form method="post" action="" class="form-horizontal" id="reset_pwd_frm" onsubmit="return false;">
					<h1>Imposta una nuova password</h1>
					<br />
					<div class="well">
						<input type="hidden" name="username" value="<?php print $hash[0]; ?>" />
						<div class="input-group">
							<label for="password" class="control-label input-group-addon btn"><span class="glyphicon glyphicon-lock" title="password"></span></label>
							<input type="password" class="form-control input-lg" id="password" name="password" placeholder="Una password valida" autocomplete="off" tabindex="1" />
						</div>
						<br />
						<div class="input-group">
							<label for="password2" class="control-label input-group-addon btn"><span class="glyphicon glyphicon-lock" title="password"></span></label>
							<input type="password" class="form-control input-lg" id="password2" name="password2" placeholder="Ripeti password" autocomplete="off" tabindex="2" />
						</div>
					</div>
					<button id="reset_btn" class="btn btn-primary right" tabindex="3">Resetta</button>
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