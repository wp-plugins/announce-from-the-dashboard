jQuery(document).ready(function($) {

	var $Form = $("#afd");

	$.post(ajaxurl, {
		'action': 'afd_get_donation_toggle',
	}, function(response){
		if( response == "1" ) {
			donation_toggle_set( true );
		}
	});

	function donation_toggle_set( s ) {
		if( s ) {
			$Form.addClass('full-width');
		} else {
			$Form.removeClass('full-width');
		}
	}


	$(".toggle-plugin .icon a" , $Form ).click(function() {

		if( $Form.hasClass('full-width') ) {
			donation_toggle_set( false );
			$.post(ajaxurl, {
				'action': 'afd_set_donation_toggle',
				'f': 0,
			});

		} else {
			donation_toggle_set( true );
			$.post(ajaxurl, {
				'action': 'afd_set_donation_toggle',
				'f': 1,
			});
		}

		return false;
	});


	var $UpdateForm = $("#afd-lists");

	$("#update table tbody tr").hover(function() {
		$("td.title .menu" , $(this) ).show();
	}, function() {
		$("td.title .menu" ).hide();
	});

	$(".menu a.menu_edit" , $UpdateForm).click(function() {
		var Tr = $(this).parent().parent().parent().parent();
		$("td .toggle" , Tr ).hide();
		$("td .edit" , Tr ).show();
		$("td.content" , Tr).css("background" , "none");
		
		$(this).parent().parent().remove();
		return false;
	});

	$('.date_range input[type=checkbox]').on('click', function() {
		var $DataRange = $(this).parent().parent().parent();
		$DataRange.children('.date_range_setting').slideToggle()
	});
	
	$('.date_range input[type=checkbox]').each(function( key, el ) {
		if( $(this).prop('checked') ) {
			var $DataRange = $(el).parent().parent().parent();
			$DataRange.children('.date_range_setting').show();
		}
	});


});
