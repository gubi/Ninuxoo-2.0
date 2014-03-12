info = {
	title: (($("#ebook_title").length > 0) ? $("#ebook_title").text() : ""),
	author: (($("#ebook_author").length > 0) ? $("#ebook_author").text() : ""),
	isbn: (($("#ebook_isbn").length > 0) ? $("#ebook_isbn").text() : ""),
	tags: (($("#ebook_subject").length > 0) ? $("#ebook_subject").text() : ""),
	publisher: (($("#ebook_publisher").length > 0) ? $("#ebook_publisher").text() : ""),
	creation_date: (($("#ebook_creation_date").length > 0) ? $("#ebook_creation_date").text() : ""),
	pages: (($("#ebook_pages").length > 0) ? $("#ebook_pages").text() : ""),
	size: (($("#ebook_size").length > 0) ? $("#ebook_size").text() : ""),
	encrypted: (($("#ebook_encrypted").length > 0) ? $("#ebook_encrypted").text() : ""),
	program: (($("#ebook_program").length > 0) ? $("#ebook_program").text() : ""),
	web_optimized: (($("#ebook_optimized").length > 0) ? $("#ebook_optimized").text() : "")
};
// Get title data
var readonly = (($("#user").text().length > 0) ? false : true);
$.get_rating(info, function(rates) {
	$("#rating").rating({
		startRate: rates.medium_rates,
		total: rates.total,
		readOnly: readonly
	}, function(selected) {
		var infoo = info;
		infoo.rate = selected;
		infoo.user = $("#user").text();
		
		$.cryptAjax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: $.jCryption.encrypt($.param(infoo), password),
				type: "set_rating"
			},
			success: function(data) {
				$("#rating").html("").rating({
					startRate: data.medium_rates,
					total: data.total,
					readOnly: readonly
				});
			}
		});
	});
});
$.get_semantic_data({type: "book", title: info.title}, function(semantic_data) {
	if(semantic_data !== null) {
	}
});