function toggle_send_notice_btn() {
	$("#send_notice_area > .input-group").removeClass("has-success");
	$("#send_notice_btn").removeClass("btn-success").addClass("btn-primary").html('Invia&nbsp;&nbsp;<span class="glyphicon glyphicon-share-alt"></span>');
	if($("#send_notice").val().length > 0 && $("#send_notice").val() != $("#send_previous_notice").val()) {
		$("#send_notice_btn").removeClass("disabled");
	} else {
		$("#send_notice_btn").addClass("disabled");
	}
}
function filter(that) {
	var tableBody = $("#dash_notifications"),
	tableRowsClass = $("#dash_notifications tr"),
	items = $("#dash_notifications tr.notice").length;
	
	$(".search-sf").remove();
	tableRowsClass.each(function(i, val) {
		var rowText = $(val).text().toLowerCase();
		var inputText = $(that).val().toLowerCase();
		if(inputText != "") {
			$(".search-query-sf").remove();
			tableBody.prepend('<tr class="search-query-sf"><td colspan="3"><span class="info"><span class="glyphicon glyphicon-sort"></span>&nbsp;Filtro su "' + $(that).val() + '" (nascost' + (((items-1) == 1) ? 'o ' : 'i ') + (items-1) + ')</span></td></tr>');
		} else {
			$(".search-query-sf").remove();
		}
		
		if(rowText.indexOf(inputText) == -1 ) {
			tableRowsClass.eq(i).hide();
		} else {
			$(".search-sf").remove();
			tableRowsClass.eq(i).show();
		}
	});
	if(tableRowsClass.children(":visible").length == 0) {
		tableBody.append('<tr class="search-sf"><td class="info" colspan="3" align="center">Nessun risultato.</td></tr>');
	}
}
function remove_notice() {
	$("#send_notice_area").attr("disabled", "disabled");
	var password = makeid();
	$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
		var encryptedString = $.jCryption.encrypt("host=" + $("#user_data").val(), password);
		
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "text",
			type: "POST",
			data: {
				jCryption: encryptedString,
				type: "remove_notify"
			},
			success: function(response) {
				$(".remove_notice_btn").addClass("disabled").closest("tr").fadeOut(600, function() { $(this).remove(); });
				$("#send_previous_notice").val("");
				if($("#send_notice_area .input-group-addon").length > 0) {
					$("#send_notice_area .input-group-addon").remove();
				}
				$("#send_notice_area").attr("disabled", false);
				toggle_send_notice_btn();
				$("#check_loader").fadeIn(600);
				setTimeout(function() {
					check_notify("true");
				}, 3000);
			}
		});
	});
}
function send_notice() {
	$("#send_notice_area").attr("disabled", "disabled");
	var password = makeid();
	$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
		var encryptedString = $.jCryption.encrypt("message=" + $("#send_notice").val() + "&host=" + $("#user_data").val(), password);
		
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "text",
			type: "POST",
			data: {
				jCryption: encryptedString,
				type: "send_notify"
			},
			success: function(response) {
				$("#send_notice_area > .input-group").addClass("has-success");
				if($("#send_notice_area .input-group-addon").length == 0) {
					$("#send_notice_area .input-group").prepend('<span class="input-group-addon">#' + response + '</span>');
				} else {
					$("#send_notice_area .input-group-addon").text("#" + response);
				}
				$("#send_notice_btn").removeClass("btn-primary").addClass("btn-success disabled").html('Inviato&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></span>');
				$("#send_notice_area").attr("disabled", false);
				$("#check_loader").fadeIn(600);
				setTimeout(function() {
					check_notify("true");
				}, 1000);
			}
		});
	});
}
$(document).ready(function() {
	var activeSystemClass = $(".list-group-item.active");

	$("#system-search").bind("keyup change", function() {
		filter(this);
	});
	$("#check_notify_btn").click(function() {
		check_notify();
	});
	$("#send_notice_btn").click(function() {
		if($("#send_notice").val().length > 0) {
			send_notice();
		}
	});
	$("#send_notice").bind("change keyup", function() {
		toggle_send_notice_btn();
	});
	toggle_send_notice_btn();
});
