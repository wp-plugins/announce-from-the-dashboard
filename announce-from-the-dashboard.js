jQuery(document).ready(function($) {

	var $Form = $("#announce_from_the_dashboard");

	// create submit
	$("p.submit input:button").click(function() {
		$Form.submit();
	});

	// update
	var $UpdateTr = $Form.children("div#update").children("table").children("tbody").children("tr");
	$UpdateTr.children("td.title").children("input").hide();
	$UpdateTr.children("td.title").children("select").hide();
	$UpdateTr.children("td.content").children(".wp-editor-wrap").hide();
	$UpdateTr.children("td.role").children("label").hide();
	$UpdateTr.children("td.operation").children("p.submit").hide();

	$UpdateTr.children("td.operation").children("span").children("a.edit").click(function() {
		var $ParentTr = $(this).parent().parent().parent();
		$ParentTr.children("td.title").children("span").hide();
		$ParentTr.children("td.title").children("input").show();
		$ParentTr.children("td.title").children("select").show();
		$ParentTr.children("td.content").children("span").hide();
		$ParentTr.children("td.content").children(".wp-editor-wrap").show();
		$ParentTr.children("td.content").css("background-color", "white");
		$ParentTr.children("td.role").children("span").hide();
		$ParentTr.children("td.role").children("label").show();
		$(this).parent().hide();
		$(this).parent().parent().children("p.submit").show();

		return false;
	});

});
