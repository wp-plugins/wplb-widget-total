
jQuery(document).ready(function($){
	var wplb_admin_upload;
	var wplb_admin_selector;

	$('.remove-image, .remove-file').on('click', function() {
		wplb_admin_remove_file( $(this).parents('.section') );
    });

    $('.upload-button').on('click', function(event) {
    	wplb_admin_add_file(event, $(this).parents('.section'));
    });

});

jQuery(document).ajaxComplete(function($) {
	jQuery('.remove-image, .remove-file').on('click', function() {
		wplb_admin_remove_file( jQuery(this).parents('.section') );
    });

    jQuery('.upload-button').live('click', function(event) {
    	wplb_admin_add_file(event, jQuery(this).parents('.section'));
    });

});

function wplb_admin_add_file(event, selector) {

	var wplb_admin_upload;
	var wplb_admin_selector;
	var upload = jQuery(".uploaded-file"), frame;
	var $el = jQuery(this);
	wplb_admin_selector = selector;

	event.preventDefault();

	// If the media frame already exists, reopen it.
	if ( wplb_admin_upload ) {
		wplb_admin_upload.open();
	} else {
		// Create the media frame.
		wplb_admin_upload = wp.media.frames.wplb_admin_upload =  wp.media({
			// Set the title of the modal.
			title: $el.data('choose'),

			// Customize the submit button.
			button: {
				// Set the text of the button.
				text: $el.data('update'),
				// Tell the button not to close the modal, since we're
				// going to refresh the page when the image is selected.
				close: false
			}
		});

		// When an image is selected, run a callback.
		wplb_admin_upload.on( 'select', function() {
			// Grab the selected attachment.
			var attachment = wplb_admin_upload.state().get('selection').first();
			wplb_admin_upload.close();
			wplb_admin_selector.find('.upload').val(attachment.attributes.url);
			if ( attachment.attributes.type == 'image' ) {
				wplb_admin_selector.find('.screenshot').empty().hide().append('<img width="100px" src="' + attachment.attributes.url + '">').slideDown('fast');
			}
			wplb_admin_selector.find('.upload-button').unbind().addClass('remove-file').removeClass('upload-button').val(wplb_admin_l10n.remove);
			wplb_admin_selector.find('.wplb-background-properties').slideDown();
			wplb_admin_selector.find('.remove-image, .remove-file').on('click', function() {
				wplb_admin_remove_file( jQuery(this).parents('.section') );
			});
		});

		
	}

	// Finally, open the modal.
	wplb_admin_upload.open();
}

function wplb_admin_remove_file(selector) {
	selector.find('.remove-image').hide();
	selector.find('.upload').val('');
	selector.find('.wplb-background-properties').hide();
	selector.find('.screenshot').slideUp();
	selector.find('.remove-file').unbind().addClass('upload-button').removeClass('remove-file').val(wplb_admin_l10n.upload);
	// We don't display the upload button if .upload-notice is present
	// This means the user doesn't have the WordPress 3.5 Media Library Support
	if ( jQuery('.section-upload .upload-notice').length > 0 ) {
		jQuery('.upload-button').remove();
	}
	selector.find('.upload-button').on('click', function(event) {
		wplb_admin_add_file(event, jQuery(this).parents('.section'));
	});
}

 
