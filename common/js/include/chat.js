$.chat_me = function(id, username) {
	if(password == undefined) {
		var password = makeid();
	}
	$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
		var encryptedString = $.jCryption.encrypt("id=" + id + "&username=" + username, password);
		
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: encryptedString,
				type: "set_socket"
			},
			success: function(response) {
				if(response == "ok") {
					console.log("ok");
				}
			}
		});
	});
};
$.check_connected = function() {
	var text_class = "", a_disabled = "false", icon= "", op = "", peoples_list = "";
	
	if(password == undefined) {
		var password = makeid();
	}
	$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
		var encryptedString = $.jCryption.encrypt("current=" + $("#user_id").text(), password);
		
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: encryptedString,
				type: "check_online_peoples"
			},
			success: function(response) {
				if(response.count > 0) {
					$.each(response.peoples, function(i, person) {
						switch(person.status) {
							case "online":
								text_class ="text-success";
								a_disabled = "false";
								icon = "fa-check-circle";
								op = 1;
								break;
							case "do_not_disturb":
								text_class ="text-danger";
								a_disabled = "true";
								icon = "fa-times-circle";
								op = 0.5;
								break;
							case "out":
								text_class ="text-muted";
								a_disabled = "false";
								icon = "fa-circle-o";
								op = 0.5
								break;
						}
						peoples_list += '<div class="media" style="opacity: ' + op + '" id="' + person.id + '"><a class="pull-left thumbnail" style="margin-bottom: 0;" onclick="' + ((a_disabled == "true") ? '' : '$.chat_me(\'' + person.id + '\', \'' + $("#user_email").text() + '\');') + '" href="javascript:void(0);"><img class="media-object" alt="' + person.name + '" src="' + person.img + '" /></a><div class="media-body"><p class="media-heading text-primary"><a style="margin-bottom: 0;" onclick="' + ((a_disabled == "true") ? '' : '$.chat_me(\'' + person.id + '\', \'' + $("#user_email").text() + '\');') + '" href="javascript:void(0);">' + person.name + ' <sup class="fa ' + icon + ' ' + text_class + '"></sup></a></p><small class="text-muted">' + person.personal_message + '</small></div></div>';
					});
					$("#online_peoples").html(peoples_list);
				} else {
					$("#online_peoples").html('<small class="info text-centered">Nessun utente connesso in questo momento</small>');
				}
			}
		});
	});
	setTimeout(function() {
		$.check_connected();
	}, 6000);
};
$(document).ready(function() {
	$.check_connected();
	
	$(".chat_status").click(function() {
		var status = $(this).attr("id").replace("chat_", "");
		
		if(password == undefined) {
			var password = makeid();
		}
		$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
			var encryptedString = $.jCryption.encrypt("username=" + $("#user_email").text() + "&status=" + status, password);
			
			$.ajax({
				url: "common/include/funcs/_ajax/decrypt.php",
				dataType: "json",
				type: "POST",
				data: {
					jCryption: encryptedString,
					type: "change_chat_status"
				},
				success: function(response) {
					$.each($("#chat_" + response.status).closest("ul").find("li"), function(i, item) {
						$(this).removeClass("active");
						if($(this).find("a > span").attr("class") != undefined) {
							$(this).find("a > span").attr("class", $(this).find("a > span").attr("class").replace("_", ""));
						}
					});
					$("#change_status_btn").addClass("disabled").html('<span class="text-muted"><span class="fa fa-spinner fa-spin"></span>&nbsp;<span class="caret"></span></span>');
					switch(response.status) {
						case "online":
							$("#change_status_btn").removeClass("disabled").html('<span class="text-success"><span class="fa fa-user"></span>&nbsp;<span class="caret"></span></span>');
							$("#chat_" + response.status).closest("li").addClass("active").find("a > span").attr("class", "text-success_");
							break;
						case "do_not_disturb":
							$("#change_status_btn").removeClass("disabled").html('<span class="text-danger"><span class="fa fa-user"></span>&nbsp;<span class="caret"></span></span>');
							$("#chat_" + response.status).closest("li").addClass("active").find("a > span").attr("class", "text-danger_");
							break;
						case "out":
							$("#change_status_btn").removeClass("disabled").html('<span class="text-muted"><span class="fa fa-user"></span>&nbsp;<span class="caret"></span></span>');
							$("#chat_" + response.status).closest("li").addClass("active").find("a > span").attr("class", "text-muted_");
							break;
					}
				}
			});
		});
		$("#chat_message").focus();
	});
});