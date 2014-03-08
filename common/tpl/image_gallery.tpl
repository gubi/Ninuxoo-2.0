<div id="blueimp-gallery" class="blueimp-gallery">
	<div class="slides"></div>
	<h3 class="title"></h3>
	<a class="prev">‹</a>
	<a class="next">›</a>
	<a class="close">×</a>
	<a class="play-pause"></a>
	<ol class="indicator"></ol>
	<!-- The modal dialog, which will be used to wrap the lightbox content -->
	<div class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" aria-hidden="true">&times;</button>
					<h4 class="modal-title"></h4>
				</div>
				<div class="modal-body next"></div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default pull-left prev">
					<i class="glyphicon glyphicon-chevron-left"></i>
					Precedente
					</button>
					<button type="button" class="btn btn-primary next">
					Prossimo
					<i class="glyphicon glyphicon-chevron-right"></i>
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<link rel="stylesheet" href="common/js/Bootstrap-Image-Gallery-3.1.0/css/blueimp-gallery.min.css">
<link rel="stylesheet" href="common/js/Bootstrap-Image-Gallery-3.1.0/css/bootstrap-image-gallery.min.css">
<script src="common/js/Bootstrap-Image-Gallery-3.1.0/js/jquery.blueimp-gallery.min.js"></script>
<script src="common/js/Bootstrap-Image-Gallery-3.1.0/js/bootstrap-image-gallery.js"></script>
<script type="text/javascript">
if($("#links").length > 0) {
	blueimp.Gallery(
		document.getElementById("links").getElementsByTagName("a"), {
			container: '#blueimp-gallery',
			carousel: true
		}
	);
}
</script>