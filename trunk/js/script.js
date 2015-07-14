;(function($){

	var WPLB_Widgets_Frontend = {

		init: function (){
			$( "select.wplb-drop-taxonomy-widget" ).on('change', this.drop.select);
			$( ".wplb-custom-taxonomy-widget input:radio.iterm_radio" ).on('change', this.list.change);
		},
		drop:{
			select:function(){
				var term_id = $(this).val();
				var taxonomy = $(this).parent().find('input.taxonomy_val').val();
				$.ajax({
			        type : "post",
			        url : wplb_js_var.ajaxurl,
			        data : {
			        	action: "wplb_widget_get_term_action", 
			        	term_id : term_id, 
			        	taxonomy:taxonomy,
			        	nonce: wplb_js_var.nonce
			        },
			        success: function(res) {
			         	res = JSON.parse(res);
			            if(res.term_url){
			            	location.href=res.term_url;
			            }
			        }	
			    }) 
			}	
		},

		list:{
			change:function(){
				var term_id = $(this).val();
				var taxonomy = $(this).data('taxonomy');
				$.ajax({
			        type : "post",
			        url : wplb_js_var.ajaxurl,
			        data : {
			        	action: "wplb_widget_get_term_action", 
			        	term_id : term_id, 
			        	taxonomy:taxonomy,
			        	nonce: wplb_js_var.nonce
			        },
			        success: function(res) {
			         	res = JSON.parse(res);
			            if(res.term_url){
			            	location.href=res.term_url;
			            }
			        }	
			    })
			}
		}
		
	}

	$(function ($){
		WPLB_Widgets_Frontend.init();
		$(".toggle").click(function(e){
			var parent = $(this).parent();
	        $(parent.find('.children:first')).collapse('toggle');
	        $(parent).removeClass('collapsed');
	        $(parent.find('.children:first')).on('shown.wplb-collapse', function(){
		        $(parent).addClass('collapsed')
		    });
	    });
	});
	
})(jQuery);