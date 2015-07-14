jQuery(document).ready(function($){
    $('.featured-toggle').on("click",function(e){
        e.preventDefault();
        var _el=$(this);
        var post_id=$(this).attr('data-post-id');
        var data={action:'toggle-featured',post_id:post_id};

        $.ajax({
			url:ajaxurl,
			data:data,
			type:'post',
			dataType:'json',

            success:function(data){
	            _el.removeClass('dashicons-star-filled').removeClass('dashicons-star-empty');
	            $("#featured-post-filter span.count").text("("+data.total_featured+")");
	            
	            if(data.new_status=="yes"){
	                _el.addClass('dashicons-star-filled');
	            }else{
	                _el.addClass('dashicons-star-empty');
	            }
            }
        
            
        });
    });
});