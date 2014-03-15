jQuery(document).ready(function($) {

	var Form = '#afd';
	var UpdateForm = '#afd-lists';

	// Toggle menu
	$(document).on('mouseenter', UpdateForm + ' table tbody tr', function( ev ) {
		$(this).find('td.title .menu').show();
	}).on('mouseleave', UpdateForm + ' table tbody tr', function( ev ) {
		$(this).find('td.title .menu').hide();
	});

	// Toggle editable display
	$(document).on('click', UpdateForm + ' td.title .menu a.menu_edit', function( ev ) {
		var TR = $(this).parent().parent().parent().parent();
		TR.find('.toggle').remove();
		TR.find('.edit').show();
		TR.find('td.content').css('background', 'none');
		
		return false;
	});
	
	// Delete confirm
	$(document).on('click', UpdateForm + ' table tbody tr td.title .menu a.delete', function( ev ) {
		var Title = $(this).parent().parent().parent().find('.announce_title strong').text();
		var ID = $(this).prop('id').replace('delete_', '');

		var $Dialog = $('#Confirm');
		$Dialog.find('a#deletebtn').prop('title', ID);
		$Dialog.find('p strong').text( Title );

		tb_show(afd.msg.delete_confirm, '#TB_inline?height=200&width=300&inlineId=Confirm', '');
		return false;
	});

	// Delete cancel
	$(document).on('click', '#ConfirmSt a#cancelbtn', function( ev ) {
		var $Dialog = $('#ConfirmSt');
		$Dialog.find('a#deletebtn').prop('title', '');
		$Dialog.find('p strong').text('');

		tb_remove();
	});

	// Delete
	$(document).on('click', '#ConfirmSt a#deletebtn', function( ev ) {
		var $Form = $('form#afd_delete_form');
		$Form.append('<input type="hidden" name="data[delete][' + $(this).prop('title') + '][id]" value="1" />');

		$Form.submit();
		return false;
	});

	// Bulk Delete
	$(document).on('click', UpdateForm + ' input[type=submit].bulk', function( ev ) {
		var Action = $(this).parent().find('select.action_sel option:selected').val();

		if( Action != "" ) {

			$Form = $(document).find('form#afd_update_form');
			if( confirm( afd.msg.bulk_delete_confirm ) ){
				return true;
			} else {
				return false;
			}

		} else {
			return false;
		}
	});

	// Toggle date field
	$(document).on('click', '.date_range input.date_range_check', function() {
		var $DataRange = $(this).parent().parent().parent();
		$DataRange.find('.date_range_setting').slideToggle()
	});
	
	// Show date field
	$('.date_range input.date_range_check').each(function( key, el ) {
		if( $(this).prop('checked') ) {
			var $DataRange = $(el).parent().parent().parent();
			$DataRange.find('.date_range_setting').show();
		}
	});

	// Date range check
	$(document).on('keyup blur change', '.date_range_setting input[type=text], .date_range_setting select', function( ev ) {
		
		var $DateRange = $(ev.target).parent().parent().parent().parent();
		
		var CheckStart = $DateRange.find('input.date_range_check').eq(0).prop('checked');
		var CheckEnd = $DateRange.find('input.date_range_check').eq(1).prop('checked');
		
		if( CheckStart && CheckEnd ) {

			var StartDate = {};
			var EndDate = {};

			StartDate.aa = $DateRange.find('.start input.date_aa').val();
			StartDate.mm = $DateRange.find('.start select.date_mm option:selected').val();
			StartDate.jj = $DateRange.find('.start input.date_jj').val();
			StartDate.hh = $DateRange.find('.start input.date_hh').val();
			StartDate.mn = $DateRange.find('.start input.date_mn').val();
			StartDate.date = new Date( StartDate.aa, StartDate.mm, StartDate.jj, StartDate.hh, StartDate.mn );
			StartDate.mic = StartDate.date.getTime();

			EndDate.aa = $DateRange.find('.end input.date_aa').val();
			EndDate.mm = $DateRange.find('.end select.date_mm option:selected').val();
			EndDate.jj = $DateRange.find('.end input.date_jj').val();
			EndDate.hh = $DateRange.find('.end input.date_hh').val();
			EndDate.mn = $DateRange.find('.end input.date_mn').val();
			EndDate.date = new Date( EndDate.aa, EndDate.mm, EndDate.jj, EndDate.hh, EndDate.mn );
			EndDate.mic = EndDate.date.getTime();

			if( StartDate.mic >= EndDate.mic ) {
				$DateRange.find('.date_range_error').fadeIn();
			} else {
				$DateRange.find('.date_range_error').hide();
			}

		} else {
			$DateRange.find('.date_range_error').hide();
		}
		
	});

	// Edit order
	$(UpdateForm + ' table tbody').sortable({
		placeholder: "widget-placeholder",
		cursor: 'move',
		distance: 2,
		stop: function(e,ui) {

			ui.item.find('th.check-column .spinner').css('display', 'block');
			ui.item.addClass('sorted');

			var sorted = new Array();
			ui.item.parent().children('tr').each(function( i , el ) {
				sorted.push($(el).prop('id').replace('tr_', '' ));
			});

			$.post(ajaxurl, {
				'action': 'afd_sort_settings',
				'sort': sorted,
			}, function(res){
				/*
				if( res.success && res.data.msg !== undefined ) {
					//console.log(res);
				}
				*/
				ui.item.find('th.check-column .spinner').css('display', 'none');
			});

		},
	});

	// donation toggle
	$.post(ajaxurl, {
		'action': 'afd_get_donation_toggle',
	}, function(response){
		if( response == "1" ) {
			donation_toggle_set( true );
		}
	});

	function donation_toggle_set( s ) {
		if( s ) {
			$(Form).addClass('full-width');
		} else {
			$(Form).removeClass('full-width');
		}
	}

	$('.toggle-plugin .icon a').on('click', function( ev ) {

		if( $(Form).hasClass('full-width') ) {
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

});
