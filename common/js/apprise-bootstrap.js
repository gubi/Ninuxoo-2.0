// Apprise for Bootstrap by Alessandro Gubitosi
//
// based on Apprie 1.5 by Daniel Raftery
// http://thrivingkings.com/apprise

function apprise(string, args, callback) {
	if(typeof(string) == "object") {
		callback = args;
		args = string;
		string = "";
	}
	var default_args = {
		"confirm": false, 			// Ok and Cancel buttons
		"verify": false, 			// Yes and No buttons
		"input": false, 			// Text input (can be true or string for default text)
		"message": false, 			// Textarea (can be true or string for default text)
		"inverted": false,
		"textOk": "Ok", 			// Ok button default text
		"textCancel": "Annulla",		// Cancel button default text
		"textYes": "Si", 			// Yes button default text
		"textNo": "No", 			// No button default text
		"icon": "",
		"fa_icon": "",
		"title": "",
		"progress": false,
		"allowExit": false
	}
	if (args) {
		for (var index in default_args) {
			if (typeof(args[index]) == "undefined") args[index] = default_args[index];
		}
	}
	var modal = $('<div class="modal fade" id="apprise" tabindex="-1" role="dialog" aria-labelledby="appriseLabel" aria-hidden="true"' + ((!args["allowExit"]) ? ' data-backdrop="static"' : '') + '></div>'),
	dialog = $('<div class="modal-dialog">'),
	content = $('<div class="modal-content">'),
	header = $('<div class="modal-header">'),
	title = $('<h4 class="modal-title">'),
	close = $('<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'),
	body = $('<div class="modal-body">'),
	row = $('<div class="row">'),
	panel = $('<div>'),
	footer = $('<div class="modal-footer" style="margin-top: 0;">');
	
	if (args) {
		if (args["title"]) {
			if (args["title"].length > 0) {
				if (args["icon"]) {
					switch(args["icon"]) {
						case "success":
							args["icon"] = "fa-check";
							title_class = " text-success";
							break;
						case "warning":
							args["icon"] = "fa-exclamation-triangle";
							title_class = " text-warning";
							break;
						case "error":
							args["icon"] = "fa-times";
							title_class = " text-danger";
							break;
						default:
							title_class = " text-primary";
							break;
					}
					var title_icon = '<span class="fa ' + args["icon"] + '"></span>&nbsp;&nbsp;';
				} else {
					var title_icon = "";
				}
				title.addClass(title_class).append(title_icon + args["title"]);
				
				if(args["fa_icon"]) {
					row.prepend('<div class="col-sm-2 text-muted"><span style="font-size: 81px;" class="fa ' + args["fa_icon"] + '"></span></div>');
				}
			}
		}
		header.appendTo(content);
		if(args["allowExit"]) {
			close.appendTo(header);
		}
		title.appendTo(header);
	}
	if(string != undefined) {
		if(string.length > 0) {
			if(args["fa_icon"]) {
				panel.addClass("col-sm-10");
			} else {
				panel.addClass("col-sm-12");
			}
			if(string.length > 0) {
				row.appendTo(body);
				panel.append(string).appendTo(row);
				body.appendTo(content);
			}
		}
	}
	
	if (args) {
		if (args["input"]) {
			if (typeof(args["input"]) == 'string') {
				row.append('<input type="text" class="form-control" value="' + args["input"] + '" />');
			} else {
				row.append('<input type="text" class="form-control" />');
			}
		}
		if (args["message"]) {
			if (typeof(args["message"]) == 'string') {
				row.append('<textarea rows="5" class="form-control">' + args["message"] + '</textarea></div>');
			} else {
				if(args["fa_icon"]) {
					row.find("div.col-sm-10").append('<textarea rows="5" class="form-control"></textarea></div>');
				} else {
					row.append('<textarea rows="5" class="form-control"></textarea></div>');
				}
			}
		}
	}
	if (args) {
		var btn_group = $('<div class="btn-group">');
		if (args["confirm"] || args["input"] || args["message"]) {
			btn_group.append('<button value="cancel" data-dismiss="modal" class="btn btn-default">' + args["textCancel"] + '</button>');
			btn_group.append('<button value="ok" data-dismiss="modal" class="btn btn-primary right">' + args["textOk"] + '</button>');
			btn_group.appendTo(footer);
		} else if (args["inverted"]) {
			btn_group.append('<button value="ok" data-dismiss="modal" class="btn btn-default">' + args["textOk"] + '</button>');
			btn_group.append('<button value="cancel" data-dismiss="modal" class="btn btn-primary right">' + args["textCancel"] + '</button>');
			btn_group.appendTo(footer);
		} else if (args["verify"]) {
			btn_group.append('<button value="cancel" data-dismiss="modal" class="btn btn-default">' + args["textNo"] + '</button>');
			btn_group.append('<button value="ok" data-dismiss="modal" class="btn btn-primary right">' + args["textYes"] + '</button>');
			btn_group.appendTo(footer);
		} else if(args["progress"]) {
			footer.append('<div class="progress progress-striped active" style="margin: 0;"><div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div></div>');
		} else {
			footer.append('<button value="ok" data-dismiss="modal" class="btn btn-default right">' + args["textOk"] + '</button>');
		}
	} else {
		footer.append('<button value="ok" data-dismiss="modal" class="btn btn-default right">Ok</button>');
		$("#apprise .modal-footer, #apprise .progress").css({"margin": "0px"});
	}
	
	dialog.appendTo(modal);
	content.appendTo(dialog);
	footer.appendTo(content);
	modal.prependTo('body');
	$(".btn").click(function() {
		if (callback && typeof(callback) === "function") {
			if(args["input"] || args["message"]) {
				callback(($('.form-control').val().length > 0) ? $('.form-control').val() : false);
			} else {
				callback(($(this).val() == "ok") ? true : false);
			}
		}
	});
	$("#apprise").modal(modal).on("shown.bs.modal", function() {
		$(this).find(".form-control").focus();
		$(document).keydown(function (e) {
			if (e.keyCode == 13) {
				if(!args["input"] && !args["message"]) {
					$('button[value="ok"]').click();
				}
			}
			if(args["allowExit"]) {
				if (e.keyCode == 27) { $("#apprise").modal("hide"); }
			}
		});
	});
}