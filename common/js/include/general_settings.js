$.switch_caching = function() {
	if($("#allow_caching > span").hasClass("fa-check-square-o")) {
		$(".caching_active").animate({opacity: "1"}, 300);
		$(".caching_active input, #caching_active label, #caching_active a").attr("disabled", false);
	} else {
		$(".caching_active").animate({opacity: "0.5"}, 300);
		$(".caching_active input, #caching_active label, #caching_active a").attr("disabled", "disabled");
	}
}
$(document).ready(function() {
	var current_session_length = $("#session_length").val();
	$("#session_length").on("keyup change", function() {
		$(this).get_duration({timetype: "seconds"});
		if($(this).val() != current_session_length) {
			$(this).closest("div").find(".help-block").fadeIn(300);
		} else {
			$(this).closest("div").find(".help-block").delay(1000).fadeOut(900);
		}
	});
	$("#meteo_refresh").on("keyup change", function() {
		$(this).get_duration();
	});
	
	$("#session_length").get_duration({timetype: "seconds"});
	$("#meteo_refresh").get_duration();
	if(window.location.hash) {
		var hash = window.location.hash.substring(1).replace(/\s+/g, "_"),
		target = $("#" + hash).offset().top;
	} else {
		var target = $("h1").eq(1).offset().top;
	}
	$("html, body").animate({ scrollTop: target }, 300);
	
	$("#save_settings_btn").click(function() {
		$("#page_loader").fadeIn(300);
		
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: $.jCryption.encrypt($("#settings_frm").serialize(), password),
				type: "save_settings"
			},
			success: function(response) {
				if (response["data"] !== "ok") {
					var risp = response["data"].split("::");
					if(risp[0] == "error") {
						alert("Si &egrave; verificato un errore durante il salvataggio:\n" + risp[1], {icon: "error", title: "Ouch!"});
					}
				} else {
					window.location.href = "./Admin";
				}
			}
		});
		return false;
	});
	$("#show_shortcut_btn").click(function() {
		$("#shortcut_legend").slideToggle(300, function() {
			if($(this).css("display") == "none") {
				$("#show_shortcut_btn > span").text("Visualizza");
			} else {
				$("#show_shortcut_btn > span").text("Nascondi");
			}
		});
	});
	$("select").chosen({
		disable_search_threshold: 5,
		allow_single_deselect: true
	});
	$("#allow_caching").click(function() {
		if($("#allow_caching > span").hasClass("fa-check-square-o")) {
			$(this).attr("data-original-title", "Abilita il caching");
			$("#allow_caching > span").removeClass("fa-check-square-o").addClass("fa-square-o");
			$("#allow_caching_checkbox").attr("checked", false);
		} else {
			$(this).attr("data-original-title", "Disabilita il caching");
			$("#allow_caching > span").removeClass("fa-square-o").addClass("fa-check-square-o");
			$("#allow_caching_checkbox").attr("checked", "checked");
		}
		$(this).tooltip("hide");
		$.switch_caching();
	});
	$.switch_caching();
});