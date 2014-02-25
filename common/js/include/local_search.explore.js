$(document).ready(function() {
	$.cryptAjax({
		url: "common/include/funcs/_ajax/decrypt.php",
		dataType: "json",
		type: "POST",
		data: {
			jCryption: $.jCryption.encrypt("dir=" + $("#dir_hash").text(), password),
			type: "get_dir_data"
		},
		success: function(response) {
			if(response != null) {
				$.each(response, function(id, image) {
					if(image.src != null) {
						$("#" + id).find(".img").html('<img src="./Scarica:?view=true&' + image.src + '" alt="folder" style="width: 128px; height: auto;" />');
					}
					$("#" + id).find(".panel-body > .text-muted").html(image.data);
				});
			}
		}
	});
	
	$("#list").click(function(event){
		event.preventDefault();
		$(this).addClass("active");
		$("#grid").removeClass("active")
		$("#explore_content .panel-heading").switchClass("panel-heading", "panel");
		$("#explore_content .title").addClass("text-left");
		$("#explore_content .item").removeClass("grid-group-item");
		$("#explore_content .item").addClass("list-group-item");
		$("#explore_content .thumbnail").removeClass("text-center").addClass("row");
		$("#explore_content .img").addClass("col-lg-2 text-center");
		$("#explore_content .caption").addClass("col-lg-10");
	});
	$("#grid").click(function(event){
		event.preventDefault();
		$(this).addClass("active");
		$("#list").removeClass("active");
		$("#explore_content .panel-heading").switchClass("panel", "panel-heading");
		$("#explore_content .title").removeClass("text-left");
		$("#explore_content .item").removeClass("list-group-item");
		$("#explore_content .item").addClass("grid-group-item");
		$("#explore_content .thumbnail").addClass("text-center").removeClass("row");
		$("#explore_content .img").removeClass("col-lg-2 text-center");
		$("#explore_content .caption").removeClass("col-lg-10");
	});
});
