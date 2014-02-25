$.makeid = function() {
	var text = "",
	possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	
	for(var i = 0; i <= 16; i++) {
		text += possible.charAt(Math.floor(Math.random() * possible.length));
	}
	return text;
};
$.get_shares = function(remote_nas) {
	$("#root_share_dir_refresh_btn > span").addClass("fa-spin");
	$("#root_share_dir").attr("disabled", "disabled");
	if (remote_nas == undefined) {
		var remote_nas = "";
	}
	
	$.cryptAjax({
		url: "common/include/funcs/_ajax/decrypt.php",
		dataType: "json",
		type: "POST",
		data: {
			jCryption: $.jCryption.encrypt("file=" + $.rawurlencode(remote_nas), password),
			type: "get_shares"
		},
		success: function(result) {
			$("#root_share_dir_refresh_btn > span").removeClass("fa-spin");
			$("#root_share_dir").attr("disabled", false);
			if(!result["alert"]) {
				var paths = "";
				if($("#selected_shared_dirs").length > 0) {
					var selected_shares = $("#selected_shared_dirs").text().split(",");
				}
				$.each(result["shares"], function(item, data) {
					if($("#selected_shared_dirs").length > 0) {
						if(jQuery.inArray(data, selected_shares) != -1) {
							paths += '<option value="' + data + '" selected>' + data + "</option>\n";
						} else {
							paths += '<option value="' + data + '">' + data + "</option>\n";
						}
					} else {
						paths += '<option value="' + data + '" selected>' + data + "</option>\n";
					}
					$("#shared_paths").html(paths).attr("disabled", false).multiselect("rebuild");
					if($("#root_share_dir_error").length > 0) {
						$("#root_share_dir").removeClass("text-block");
					}
					$("#root_share_dir_error").remove();
					$("#root_share_dir").closest(".form-group").removeClass("has-error");
				});
			} else {
				if($("#root_share_dir_error").length == 0) {
					$("#root_share_dir").closest(".form-group").addClass("has-error").after('<span id="root_share_dir_error" class="text-block"><span class="text-danger">' + result["alert"] + '</span></span>');
				}
				$("#shared_paths").val("").attr("disabled", "disabled").multiselect("rebuild");
				apprise(result["alert"], {icon: "warning", title: "Mmmm..."}, function(r) {
					if(r) {
						$("#root_share_dir").focus();
					}
				});
			}
		}
	});
};
