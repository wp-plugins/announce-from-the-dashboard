jQuery(document).ready(function($) {

	var Form = '#afd';
	var UpdateForm = '#afd-lists';
	var $Confirm = $('#afd_confirm');
	var $DelForm = $('#afd_delete_form');

	$('#afd_create_form .announce_add_btn').on('click', function( ev ) {
		$(ev.target).parent().parent().find('#add').removeClass('hide_add');
		$(ev.target).remove();
	});

	// Toggle editable display
	$(document).on('click', UpdateForm + ' td.operation .menu a.menu_edit', function( ev ) {
		var TR = $(ev.target).parent().parent().parent().parent();
		TR.addClass('collapse');
		
		return false;
	});
	
	// Single Delete confirm
	$(document).on('click', UpdateForm + ' table tbody tr td.operation .menu a.delete', function( ev ) {
		var TR = $(ev.target).parent().parent().parent().parent();
		var Title = TR.find('.announce_title strong').text();
		var ID = $(ev.target).prop('id').replace('delete_', '');
		var delete_list = {}
		delete_list['tr_' + ID] = ID;
		
		delete_confirm_show( Title , delete_list );
		return false;
	});

	// Bulk Delete
	$(document).on('click', UpdateForm + ' input[type=button].bulk', function( ev ) {
		var Action = $(ev.target).parent().find('select.action_sel option:selected').val();

		if( Action != "" ) {
			
			var del_check = false;
			var del_list = {};

			$(document).find('#update table tbody tr.afd_list_tr').each( function( key , el ) {

				var TR = $(el);
				var $Checkbox = TR.find('th.check-column input[type=checkbox]');
				var checked = $Checkbox.prop('checked');
				if( checked ) {
					del_list[TR.prop('id')] = $Checkbox.val();
					del_check = true;
				}

			});
			
			if( del_check ) {
				var Html = '<ul>';
				for(var id in del_list) {
					Html += '<li>' + $(document).find('#update table tbody tr#' + id + ' .announce_title strong').text() + '</li>';
				}
				Html += '</ul>';

				delete_confirm_show( Html , del_list );
				return false;
			}

		}
	});

	function delete_confirm_show( html , list ) {
		$DelForm.find('.delete_id').remove();
		$Confirm.find('p strong').html( html );
		for(var key in list) {
			$DelForm.append('<input type="hidden" name="data[delete][' + list[key] + ']" class="delete_id" value="1" />');
		}
		tb_show( afd.msg.delete_confirm , '#TB_inline?height=200&width=300&inlineId=afd_confirm', '' );
		return false;
	}

	// Delete cancel
	$(document).on('click', '#ConfirmSt a#cancelbtn', function( ev ) {
		$DelForm.find('.delete_id').remove();
		$(ev.target).parent().find('strong').find('p strong').html('');
		$Confirm.find('p strong').html('');

		tb_remove();
		return false;
	});

	// Delete
	$(document).on('click', '#ConfirmSt a#deletebtn', function( ev ) {
		$DelForm.submit();
		return false;
	});

	// Toggle date field
	$(document).on('click', '.date_range input.date_range_check', function() {
		var $DataRange = $(this).parent().parent().parent();
		$DataRange.find('.date_range_setting').slideToggle()
	});
	
	// Show date field
	$('.date_range input.date_range_check').each(function( key, el ) {
		if( $(el).prop('checked') ) {
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
		handle: ".check-column",
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
				'afd_sort': sorted,
				'afd_field': afd.afd_field
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

	// Show child-sites description
	$('.select_default_subsites select.default_show').each(function( key, el ) {
		var default_show = $(el).find(':selected').val();
		$(el).parent().children('.' + default_show).show();
	}).on('change', function( ev ) {
		var default_show = $(ev.target).find(':selected').val();
		$(ev.target).parent().children('.show_subsite_description').hide();
		$(ev.target).parent().children('.' + default_show).show();
	});

	$('.afd #postbox-container-1 .toggle-width').on('click', function( ev ) {
		
		var Action = 'afd_donation_toggle';
		$('.afd').toggleClass('full-width');

		if( $('.afd').hasClass('full-width') ) {
			$.post(ajaxurl, {
				'action': Action,
				'afd_field_donate': afd_donate.afd_field_donate,
				'f': 1,
			});
		} else {
			$.post(ajaxurl, {
				'action': Action,
				'afd_field_donate': afd_donate.afd_field_donate,
				'f': 0,
			});
		}
		
		return false;

	});

});
