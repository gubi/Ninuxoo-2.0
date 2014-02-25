$(document).ready(function() {
	$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
	
	$("select:not(#shared_paths)").chosen({
		disable_search_threshold: 5,
		allow_single_deselect: true
	});
	$("#shared_paths").multiselect({
		buttonClass: 'btn btn-default',
		maxHeight: 400,
		enableCaseInsensitiveFiltering: true,
		filterPlaceholder: "Filtra...",
		includeSelectAllOption: true,
		selectAllText: "Seleziona tutte",
		selectAllValue: 'multiselect-all',
		buttonWidth: 'auto',
		buttonContainer: '<div class="btn-group" />',
		buttonText: function(options) {
			if (options.length == 0) {
				return 'Nessuna directory selezionata <b class="caret"></b>';
			} else if (options.length > 6) {
				return options.length + ' selezionate <b class="caret"></b>';
			} else {
				var selected = '';
				options.each(function() {
					selected += $(this).text() + ', ';
				});
				return selected.substr(0, selected.length -2) + ' <b class="caret"></b>';
			}
		}
	});
	$("#root_share_dir").change(function(){
		$.get_shares($("#root_share_dir").val());
		$(".multiselect-search").focus();
	});
	$("#root_share_dir_refresh_btn").click(function(){
		$.get_shares($("#root_share_dir").val());
	});
	$.get_shares($("#root_share_dir").val());
	
	$("#start_scan_btn").click(function() {
		apprise("", {title: 'Scansione del <acronym title="Network Attached Storage">NAS</acronym> in corso...', icon: "fa-magic fa-bounce", progress: "true"});
		$(this).removeClass("btn-success").addClass("btn-warning").addClass("disabled").find("span").addClass("fa-spin");
		
		$.cryptAjax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: $.jCryption.encrypt("token=" + $("#token").val(), password),
				type: "start_scan"
			},
			success: function(response) {
				$("#start_scan_btn").removeClass("disabled").toggleClass("btn-warning btn-success").find("span").removeClass("fa-spin");
				$("#last_scan_date").html(response["data"]["date"]);
				$("#last_scanning_time").html(response["data"]["elapsed_time"]);
				$("#last_items_count").html(response["data"]["files_count"]);
				$(".appriseOuter").fadeOut(300);
				$(".appriseOverlay").fadeOut(300);
				$("#apprise").modal("hide");
			}
		});
	});
	$("#save_search_params_btn").click(function() {
		$("#page_loader").fadeIn(300);
		var shared_paths = "";
		
		if($("#shared_paths option:selected").index() > 0) {
			if($("#shared_paths").val().length > 0) {
				if($("#shared_paths").val().length == 1) {
					shared_paths = $("#shared_paths").val();
				} else {
					shared_paths = $("#shared_paths").val().join(",");
				}
			}
		}
		
		$.cryptAjax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: $.jCryption.encrypt($("#search_settings_frm").serialize() + "&shared_paths=" + $.rawurlencode(shared_paths), password),
				type: "save_search_settings"
			},
			success: function(response) {
				if (response["data"] !== "ok") {
					var risp = response["data"].split("::");
					if(risp[0] == "error") {
						alert("Si &egrave; verificato un errore durante l'installazione:\n" + risp[1], {icon: "error", title: "Ouch!"});
					}
				} else {
					$("#page_loader").fadeOut(300);
				}
			}
		});
		return false;
	});
	$("#show_nas_advanced_options").click(function() {
		$(this).hide();
		$("#nas_advanced_options").slideDown(300);
		return false;
	});
});