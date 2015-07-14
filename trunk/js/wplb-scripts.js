;(function($){

	var WPLB_Widgets = {

		MediaOpen: true,

		init: function (){
			$( ".upload-button" ).on('click', this.image.Add);
			$( ".remove-image" ).on('click', this.image.Remove);
		},
		image:{
			Add:function(e){
				e.preventDefault();
				// var id = $(this).attr('id');
				var id = $(this).parent('.section-upload').attr('id');
				// Backup original functions.
				WPLB_Widgets.insert = wp.media.editor.send.attachment;
				WPLB_Widgets.embed = wp.media.string.image;
							
				// Open insert media lightbox.
				if ( typeof wp !== 'undefined' && wp.media && wp.media.editor ) {
					wp.media.editor.open({multiple: false, title: 'WPLB Image Widget', type: 'image'});
				}

				// Image was selected from Media Library.
				wp.media.editor.send.attachment = function(selection, image) {
					WPLB_Widgets.image.Selected(selection.size, image, id)
					WPLB_Widgets.image.CloseMedia();
					WPLB_Widgets.image.Update(id);
				};

				// Image was selected by URL.
				wp.media.string.image = function (image) {
					WPLB_Widgets.image.Show(image);
					WPLB_Widgets.image.CloseMedia();
					WPLB_Widgets.image.Update(id);
				}
				
				// Lightbox was closed, make sure to restore backed up functions.
				if (WPLB_Widgets.MediaOpen) {
					wp.media.frame.on('escape', function() {
						WPLB_Widgets.image.CloseMedia();
					});
				}
				
				WPLB_Widgets.MediaOpen = false;
			}

			,Remove:function(){
				var id = $(this).parent('.section-upload').attr('id');
				$('#'+id +' .screenshot').html('');
				$('#'+id+' .src').attr('value', '');
				$(this).addClass('upload-button').removeClass('remove-image').html(wplb_var.upload);
				WPLB_Widgets.image.Update(id);
			}

			,Selected:function (size, img, id ){
				var section = $('#'+id).parent('.widget-content');
				imageSize = img.sizes[size];
				if ( img.type == 'image' ) {
					$('#'+id +' .screenshot').empty().hide().append('<img max-width="100%" src="' + imageSize.url + '">').slideDown('fast');
				}

				$('#'+id+' .upload-button').unbind().addClass('remove-image').removeClass('upload-button').html(wplb_var.remove);
				$('#'+id+' .src').attr('value', imageSize.url);

				$('#widget-'+id+'-alt').attr('value', img.alt);
				$('#widget-'+id+'-caption').attr('value', img.caption);
				$('#widget-'+id+'-size').attr('value', size);

				

				WPLB_Widgets.image.Update(id);

				//wplb_admin_selector.find('.upload-button').unbind().addClass('remove-file').removeClass('upload-button').val(wplb_var.remove);
				// $(id + ' .remove-image-link').show();
				// $(id + ' .img-thumb').html('<img src="' + imageSize.url + '" style="max-width: 100%;">');
				// $(id + ' .src').attr('value', imageSize.url);
				// $(id + ' .display-width').attr('value', imageSize.width);
				// $(id + ' .display-height').attr('value', imageSize.height);
				// $(id + ' .original-width').attr('value', imageSize.width);
				// $(id + ' .original-height').attr('value', imageSize.height);
				// $(id + ' .alt').attr('value', image.alt);

				// if (image.title != '' && $(id + ' .title').attr('value') == '') {
				// 	$(id + ' .title').attr('value', image.title);
				// }


				// var attachment = wplb_admin_upload.state().get('selection').first();
				// wplb_admin_upload.close();
				// wplb_admin_selector.find('.upload').val(attachment.attributes.url);
				// if ( attachment.attributes.type == 'image' ) {
				// 	wplb_admin_selector.find('.screenshot').empty().hide().append('<img width="100px" src="' + attachment.attributes.url + '">').slideDown('fast');
				// }
				// wplb_admin_selector.find('.upload-button').unbind().addClass('remove-file').removeClass('upload-button').val(wplb_var.remove);
				// wplb_admin_selector.find('.wplb-background-properties').slideDown();
				// wplb_admin_selector.find('.remove-image, .remove-file').on('click', function() {
				// 	wplb_admin_remove_file( $(this).parents('.section') );
				// });
			}

			,CloseMedia:function(){

			}

			,Show:function(img){

			}

			,Update:function(id){
				$('#'+id + ' .title').trigger('change');
				WPLB_Widgets.init();
			}
		}
	}

	var WPLB_List_Posts = {

		MediaOpen: true,

		init: function (){
			$( ".wplb-post-term  .wplb-select-post_type select" ).on('change', this.getTaxonomyTermList);
			$( ".wplb-post-term  .wplb-select-tax select" ).on('change', this.getTermList);
			$( ".wplb-post-term  .wplb-select-term select" ).on('change', this.selectTerm);
			// $( ".wplb-select-term" ).on('change', this.select);
		},
		getTaxonomyTermList: function (){
			var post_type = $(this).val();
			
			var widget_id = $(this).data('widget_id');
			var $json = [];
			var spinner = $(this).parent().find( '.spinner' );
			spinner.addClass('active');
           $.ajax({
	         type : "post",
	         url : wplb_var.ajaxurl,
	         data : {action: "wplb_widget_ajax_action", post_type : post_type, do_action : 'get_by_post_type', nonce: wplb_var.nonce},
	         success: function(res) {
	         	res = JSON.parse(res);
	            $('#widget-'+widget_id+'-taxonomy').html(res.tax);
	            $('#widget-'+widget_id+'-term').html(res.term);
	            $json.push({
	            	post_type:post_type,
	            	taxonomy:res.tax_value,
	            	term:'all'
	            });

	            $encoded_json = JSON.stringify($json);
		        // Update the form field.
		        $('#widget-'+widget_id+'-post_and_term').val($encoded_json);
		        spinner.removeClass('active');
	         }
	      })   
		},
		getTermList: function (){

			var taxonomy = $(this).val();
			var widget_id = $(this).data('widget_id');
			var post_type = $('#widget-'+widget_id+'-post_type').val();
			var spinner = $(this).parent().find( '.spinner' );
			spinner.addClass('active');
			var $json = [];
           $.ajax({
	         type : "post",
	         url : wplb_var.ajaxurl,
	         data : {action: "wplb_widget_ajax_action", post_type : post_type,  taxonomy : taxonomy, do_action : 'get_by_taxonomy', nonce: wplb_var.nonce},
	         success: function(res) {
	         	res = JSON.parse(res);
	            $('#widget-'+widget_id+'-term').html(res.term);
	            $json.push({
	            	post_type:post_type,
	            	taxonomy:taxonomy,
	            	term:'all'
	            });
	            $encoded_json = JSON.stringify($json);
		        // Update the form field.
		        $('#widget-'+widget_id+'-post_and_term').val($encoded_json);
		        spinner.removeClass('active');
	         }
	      })   
		},
		selectTerm: function (){

			var term = $(this).val();
			var widget_id = $(this).data('widget_id');
			var post_type = $('#widget-'+widget_id+'-post_type').val();
			var taxonomy = $('#widget-'+widget_id+'-taxonomy').val();
			var $json = [];

			$json.push({
	            	post_type:post_type,
	            	taxonomy:taxonomy,
	            	term:term
	            });
            $encoded_json = JSON.stringify($json);
	        // Update the form field.
	        $('#widget-'+widget_id+'-post_and_term').val($encoded_json);  
		},
		select:function(){
			var term = $(this).val();
			var parent = $(this).parent();
			var post_type = $(parent.find('.post_type select')).val();
			var taxonomy = $(parent.find('.taxonomy select')).val();
			var $json = [];

			$json.push({
	            	post_type:post_type,
	            	taxonomy:taxonomy,
	            	term:term
	            });
            $encoded_json = JSON.stringify($json);
            parent.find('.post_and_term textarea').val($encoded_json);

		}
	}

	$(function ($){
		WPLB_Widgets.init();
		WPLB_List_Posts.init();
		return false;
	});
	$(document).ajaxComplete(function($) {
		WPLB_Widgets.init();
		WPLB_List_Posts.init();
		return false;
	});

})(jQuery);