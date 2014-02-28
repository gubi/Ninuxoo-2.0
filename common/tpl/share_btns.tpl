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
$(document).ready(function() {
	$("#share_btn").click(function() {
		$.get_page_url();
	});
});
</script>
<div class="right">
	<div class="btn-group">
		<?php
		if(isset($COOKIE["n"])){
			?>
			<a href="javascript:void(0);" class="btn btn-default" title="Mi piace"><class id="stars-existing" class="starrr text-warning" data-rating='4'></class></a>
			<?php
		}
		?>
		<a href="javascript:void(0);" class="btn btn-default" id="share_btn" data-url="" data-page-url="<?php print $page_url; ?>" title="Condividi"><i class="fa fa-share"></i></a>
	</div>
</div>
