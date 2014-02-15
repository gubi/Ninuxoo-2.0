$.get_user_pages = function(id) {
	console.log(id);
	$("#users_pages").html('<span class="info">Carico i dati...</span>').addClass("text-center");
	if(id == null || id == undefined) {
		var id = "";
	}
	var password = makeid();
	$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
		var encryptedString = $.jCryption.encrypt("user=" + id, password);
		
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: encryptedString,
				type: "get_user_pages"
			},
			success: function(response) {
				if(id.length == 0) {
					if(response.count == 0) {
						$("#users_pages").html('<span class="info">Non ci sono utenti con pagine condivise</span>').addClass("text-center");
					} else {
						$.each(response.users, function(item, user) {
							$("#users_pages").html('<div class="col-sm-3 panel"><div class="media"><a href="./Pagine/' + user.id + '" class="pull-left"><img src="' + user.gravatar + '" style="width: 50px; height: 50px; border: #ccc 1px solid;" class="media-object"></a><div class="media-body"><h5 class="media-heading"><a href="./Pagine/' + user.id + '">' + user.nick + '</a></h5><small class="text-muted">' + user.personal_message + '</small></div></div></div>');
						});
					}
				} else {
					var pages_link = "";
					console.log(response);
					if(response.count > 0 && response.pages != undefined) {
						$.each(response.pages, function(item, page) {
							pages_link += '<li><span class="fa-li fa fa-file-o"></span><a href="./Pagine/' + id + '/' + page.id + '">' + page.name + '</a></li>';
						});
						$("#users_pages").html('<div class="panel" id="pages"><ul class="fa-ul">' + pages_link + '</ul></div>').removeClass("text-center");
					} else {
						$("#users_pages").html('<span class="info">Non &egrave; stato possibile caricare le pagine degli utenti.<br />Riprovare ricaricando la pagina</span>').addClass("text-center");
					}
				}
			}
		});
	}, function() {
		$("#page_loader").fadeOut(300);
		alert("Si &egrave; verificato un errore durante la scansione.", {icon: "error", title: "Ouch!"});
	});
};