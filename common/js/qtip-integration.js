$(document).ready(function() {
	$("#top_menu ul:first a:first[title]").qtip({
		style: {
			border: {
				width: 2,
				radius: 3
			},
			color: "white",
			name: "dark",
			textAlign: "center",
			tip: true
		},
		position: {
			corner: {
				target: "bottomRight",
				tooltip: "topLeft"
			}
		}
	});
	$("#top_menu ul:first a:not(:first)[title]").qtip({
		style: {
			border: {
				width: 2,
				radius: 3
			},
			color: "white",
			name: "dark",
			textAlign: "center",
			tip: true
		},
		position: {
			corner: {
				target: "bottomMiddle",
				tooltip: "topMiddle"
			}
		}
	});
	$("#second_menu a[title], #search a[title], #resstats a[title]").qtip({
		style: {
			border: {
				width: 2,
				radius: 3
			},
			color: "white",
			name: "dark",
			textAlign: "center",
			tip: true
		},
		position: {
			corner: {
				target: "bottomLeft",
				tooltip: "topRight"
			}
		}
	});
	$("ul:not(.filetree) > li > *[title], a.show_resource_btn[title], a.copy_btn[title]").qtip({
		style: {
			border: {
				width: 2,
				radius: 3
			},
			color: "white",
			name: "dark",
			textAlign: "center",
			tip: true
		},
		position: {
			corner: {
				target: "topMiddle",
				tooltip: "bottomMiddle"
			}
		}
	});
});