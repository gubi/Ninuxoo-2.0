<script type="text/javascript">
$.get_page_url = function() {
	if($("#share_btn").attr("data-url").length > 0) {
		apprise('<h4 class="text-center"><tt>' + $("#share_btn").attr("data-url") + "</tt></h4>", {title: "Indirizzo di questa pagina", icon: "fa-globe"});
	} else {
		apprise("", {title: "Genero l'indirizzo...", icon: "fa-magic fa-bounce", progress: true});
		$.cryptAjax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "text",
			type: "POST",
			data: {
				jCryption: $.jCryption.encrypt("url=" + $("#share_btn").attr("data-page-url"), password),
				type: "get_page_url"
			},
			success: function(url) {
				$("#apprise").modal("hide");
				$("#share_btn").attr("data-url", url);
				apprise('<h4 class="text-center"><tt>' + url + "</tt></h4>", {title: "Indirizzo di questa pagina", icon: "fa-globe"});
			}
		});
	}
};
$.get_seen_txt = function() {
	var seen_txt = "";
	switch($("#result_filetype").text()) {
		case "book":
			seen_txt = "letto";
			break;
		case "audio":
			seen_txt = "ascoltato";
			break;
		case "video":
			seen_txt = "visto";
			break;
		default:
			seen_txt = "visto";
			break;
	}
	return seen_txt;
};
$.set_seen = function(status) {
	var title_txt = "",
	seen_status = "";
	if(status == "unseen") {
		title_txt = "Marca come non " + $.get_seen_txt();
		seen_status = "seen";
		$("#seen_btn").removeClass("active");
	} else {
		title_txt = "Marca come " + $.get_seen_txt();
		seen_status = "unseen";
		$("#seen_btn").addClass("active");
	}
	$("#seen_btn").addClass("disabled").find("span").removeClass("fa-heart text-danger").addClass("fa-spinner fa-spin text-muted");
	$.cryptAjax({
		url: "common/include/funcs/_ajax/decrypt.php",
		dataType: "text",
		type: "POST",
		data: {
			jCryption: $.jCryption.encrypt("url=" + $("#share_btn").attr("data-page-url") + "&user=" + $("#user_email").text() + "&status=" + seen_status, password),
			type: "set_seen"
		},
		success: function(status) {
			if(status != "no") {
				$("#seen_btn").removeClass("disabled").attr("data-original-title", title_txt).tooltip("hide");
				$("#seen_btn span").removeClass("fa-spinner fa-spin").addClass("fa-heart text-danger");
				if(status == "unseen") {
					$("#seen_btn").removeClass("active").blur();
				} else {
					$("#seen_btn").addClass("active").blur();
				}
				$("#seen_btn span").attr("id", seen_status)
			}
		}
	});
};
$.check_seen = function() {
	var title_txt = "",
	seen_status = "";
	
	$("#seen_btn").addClass("disabled").find("span").removeClass("fa-heart text-danger").addClass("fa-spinner fa-spin text-muted");
	$.cryptAjax({
		url: "common/include/funcs/_ajax/decrypt.php",
		dataType: "text",
		type: "POST",
		data: {
			jCryption: $.jCryption.encrypt("url=" + $("#share_btn").attr("data-page-url") + "&user=" + $("#user_email").text(), password),
			type: "get_seen"
		},
		success: function(status) {
			if(status == "unseen") {
				$("#seen_btn").removeClass("disabled").removeClass("active").attr("data-original-title", "Marca come " + $.get_seen_txt()).tooltip("hide");
				$("#seen_btn span").removeClass("fa-spinner fa-spin").addClass("fa-heart text-danger");
			} else {
				$("#seen_btn").addClass("active");
				$("#seen_btn").removeClass("disabled").addClass("active").attr("data-original-title", "Marca come non " + $.get_seen_txt()).tooltip("hide");
				$("#seen_btn span").removeClass("fa-spinner fa-spin").addClass("fa-heart text-danger").attr("id", status);
			}
		}
	});
}
$(document).ready(function() {
	$("#share_btn").click(function() {
		$.get_page_url();
	});
	$("#seen_btn").click(function() {
		$.set_seen($("#seen_btn span").attr("id"));
	});
	$.check_seen();
});
</script>
<div class="right">
	<div class="btn-group">
		<?php
		if(isset($_COOKIE["n"])){
			?>
			<a href="javascript:void(0);" class="btn btn-default disabled" id="seen_btn" title="Visto"><span id="unseen" class="fa fa-heart text-danger"></span></a>
			<?php
		}
		?>
		<a href="javascript:void(0);" class="btn btn-default" id="share_btn" data-url="" data-page-url="<?php print $page_url; ?>" title="Condividi"><i class="fa fa-share"></i></a>
	</div>
</div>
