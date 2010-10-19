jQuery(document).ready(function() {



	AjaxResult.add_comment = function(data) {
		var result_id = parseInt(data.message.comment_ID);

		
		
		if(data.status == 0){
			jQuery('body').displayerrorslightbox(data);
			return;
		}
		var selected_paragraph_number = parseInt(jQuery('#selected_paragraph_number').val());

		var comment_parent  = data.message.comment_parent;

		var comment_id = 'comment-' +  blog_ID + '-' + result_id;
		var parent_id = 'comment-' +  blog_ID + '-' + data.message.comment_parent;
		var depth = 'depth-1';
		if(data.message.comment_parent > 0){
			depth = 'depth-2';
		}

		var new_comment = '<div id="'+comment_id+'" class="comment byuser bypostauthor '+depth+' paragraph-'+selected_paragraph_number+'">' +
				'<div class="comment-body" id="div-'+comment_id+'">' +
					'<div class="comment-header">' +
					'<div class="comment-author vcard">' +
					'<a href="'+siteurl+'/profile/'+data.message.comment_author+'">'+data.message.comment_author+'</a>'+
					'</div>'+
					'<div class="comment-meta">'+
					'<span title="'+blog_ID+'" class="comment-blog-id"></span>'+
					'<span title="'+result_id+'" class="comment-id"></span>'+
					'<span title="0" class="comment-parent"></span>'+
					'<span title="'+selected_paragraph_number+'" class="comment-paragraph-number"></span>'+
					'<span class="comment-date">'+data.message.comment_date+'</span>'+
					'<span class="comment-icon comment-icon-quarantine"></span><span class="comment-icon comment-icon-flag"></span>'+
					'</div>'+
					'</div>'+
					'<div class="comment-text"><p>'+ jQuery('#comment').val() + '</p>' +
					'</div>' +
					'<div title="'+result_id+'" class="comment-reply comment-hover small-button" ></div>'+
					'<div class="comment-respond"></div>' +
				'</div>' +
			'</div>';

		jQuery('#no-comments').hide();

		if(comment_parent > 0){
			//we are grouping comments
			if(jQuery('#paragraph-block-' + selected_paragraph_number).length){
				jQuery('#respond').appendTo('#paragraph-block-' + selected_paragraph_number + ' .toplevel-respond');			

				jQuery('#' + parent_id).append(new_comment);			

				//jQuery('#commentbox').scrollTo('#'+comment_id , 200);
				jQuery('.comment-reply').html('reply');
			}
			else{
				//alert('nogrouping');
				if( jQuery('#' + parent_id).next().hasClass('children') ){
					jQuery('#' + parent_id + ' + .children').prepend(new_comment);										
				}
				else{
					jQuery('#' + parent_id).after('<ul class="children">' + new_comment + '</ul>');					
				}
				
			}
			jQuery('#'+comment_id).fadeIn("#"+comment_id);			
		}
		else{
			//we are grouping comments
			if(jQuery('#paragraph-block-' + selected_paragraph_number).length){
				jQuery('#respond').append(new_comment);			
				jQuery('#commentbox').scrollTo('#'+comment_id , 200);
			}
			else{
				//alert('nogrouping');
				jQuery('.commentlist').prepend(new_comment);			
				
			}
			jQuery('#'+comment_id).fadeIn("#"+comment_id);
		}
		
		var current_count = parseInt(jQuery(jQuery('.commentcount').get((selected_paragraph_number ))).html()) + 1;
		
		jQuery(jQuery('.commentcount').get((selected_paragraph_number ))).html(current_count);
		jQuery(jQuery('.commentcount').get((selected_paragraph_number ))).fadeIn('slow');
		jQuery('#comment').val('');		
		jQuery('#comment_parent').val(0);
		return;
	}




	function handlePaginationClick(new_page_index, pagination_container) {
		// This selects 20 elements from a content array
		for(var i=new_page_index;i<7;i++) {
			jQuery('#commentbox').append(content[i]);
		}
		return false;
	}

	// First Parameter: number of items
	// Second Parameter: options object
/*
	jQuery("#commentbox").pagination(122, {
		items_per_page:20, 
		callback:handlePaginationClick
	});
*/


	jQuery('.comment').hover(function (e) {
		
		if(jQuery('body').hasClass('single')){
			return;
		}
		alert('sdf');
		
		var index = jQuery('.comment').index(this);		
		if(jQuery('.comment-goto').length){
			var item = jQuery('.comment-goto').get(index);
			if(item){
				jQuery(item).show();			
			}
		}
		
	},	function (e) {
		if(jQuery('body').hasClass('single')){
			return;
		}
		
		var index = jQuery('.comment').index(this);
		if(jQuery('.comment-goto').length){
			var item = jQuery('.comment-goto').get(index);
			if(item){
				jQuery(item).hide();			
			}
		}
	});



	jQuery('.comment').click(function(e){

		var target = e.target;
		
		var comment =  jQuery(this).attr('id');
		
		
		var comment_id = jQuery('#' +comment + ' .comment-id').attr('title');
		
		
		jQuery.cookie('selected_comment', comment_id, { path: '/' , expires: 1} );				
		jQuery.cookie('selected_comment_id', comment_id, { path: '/' , expires: 1} );				



		jQuery('#comments-toolbar-sort').addClass('button-disabled');

		jQuery('.comment').removeClass('selected');
		jQuery('.moderate-comment').removeClass('disabled-button');

/*
		TEMPORARY DISABLE
		jQuery(this).addClass('selected');
		jQuery('#commentbox').scrollTo( jQuery(this), 1000);								
*/




		if(jQuery('body').hasClass('single')){
			var selected_blog_id = jQuery.cookie('selected_blog_id');				
			//document.location.hash = '#comment-' + selected_blog_id + '-' +comment_id;
		}
		/*

		if(jQuery(target).hasClass('comment-reply')){
			return;
		}
		*/
		//alert(jQuery(target).parents().hasClass('comment-respond') );
		if(!jQuery(target).parents().hasClass('comment-respond') && !jQuery(target).hasClass('comment-reply') && !jQuery('body').hasClass('page-template-moderator-php')){
			//alert(jQuery(target).hasClass('comment-reply'));
			//commentbox_open_state();			
		}
		
		
		
		if(jQuery('body').hasClass('single') && jQuery('.comment-reply').length){
			//jQuery('.comment-reply').hide();
			var index = jQuery('.comment').index(this);				
			var item = jQuery('.comment-reply').get(index);

			if(jQuery('#' + comment).hasClass('depth-3')){
				return;
			}
			else if(item){
				jQuery(item).show();			
			}
		}
	});	

	jQuery('.comment').hover(function(e){

		var selected_comment = parseInt(jQuery('#comment_parent').val());
		var current = parseInt(jQuery('.comment').index(this));	
		var item = jQuery('.comment-reply').get(current);


	}, function(e){

		var selected_comment = parseInt(jQuery('#comment_parent').val());
		var current = parseInt(jQuery('.comment').index(this));	
		var item = jQuery('.comment-reply').get(current);



	});



	/*
	jQuery('.comments-toolbar-icon').css('color', '#4A4848');

	jQuery('.comments-toolbar-icon').hover(function(e){
	
		jQuery(this).css('color', '#BBBBBB');
	});
	*/


	jQuery("#comment").focus(function (e) {


		jQuery("#submit-comment").show();

		//jQuery("#cancel-response").show();

		jQuery(".comment").removeClass('selected');
		//jQuery("#comment_parent").val('0');

		
		
	});

	//alert('sd');

	jQuery("#comment").focus(function (e) {
		if( jQuery(this).val() == 'Click here add a new comment...'){
			jQuery(this).val('');
		}
	});

	jQuery("#user_email").focus(function (e) {
		if( jQuery(this).val() == 'Email'){
			jQuery(this).val('');
		}
	});

	jQuery("#display_name").focus(function (e) {
		if( jQuery(this).val() == 'Your Name'){
			jQuery(this).val('');
		}
	});


	jQuery("#comment").keypress(function (e) {


		if(jQuery("#comment").val().length > 10){

			//jQuery('#submit-comment').removeClass('disabled');
			
		}
		else{
			
			//jQuery('#submit-comment').addClass('disabled');
		}

		
	});



	jQuery("#comments-toolbar #comment").click(function (e) {
		//jQuery('.comment-reply').hide();
	});


	jQuery("#comment").click(function (e) {
		//jQuery('form').submit();
	});




	var userAgent = navigator.userAgent.toLowerCase();

	// Figure out what browser is being used
	jQuery.browser = {
		version: (userAgent.match( /.+(?:rv|it|ra|ie|me)[\/: ]([\d.]+)/ ) || [])[1],
		chrome: /chrome/.test( userAgent ),
		safari: /webkit/.test( userAgent ) && !/chrome/.test( userAgent ),
		opera: /opera/.test( userAgent ),
		msie: /msie/.test( userAgent ) && !/opera/.test( userAgent ),
		mozilla: /mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent )
	};


	var msie7=jQuery.browser.msie && jQuery.browser.version=="7.0";
	var msie6=jQuery.browser.msie && jQuery.browser.version=="6.0";
	
	






	jQuery('.comment-reply').toggle(function (e) {

		var top = 0;
		var comment_id = jQuery(this).attr('title');
		
		var current_comment_id = '#comment-'+ blog_ID +'-'+comment_id;
		
		var paragraphnumber = jQuery(current_comment_id + ' .comment-paragraph-number').attr('title');

		var comment_id = jQuery(current_comment_id + ' .comment-id').attr('title');
		var blog_id = jQuery(current_comment_id + ' .comment-blog-id').attr('title');

		
		jQuery('#selected_paragraph_number').attr('value', paragraphnumber);
		jQuery('#comment_parent').val(comment_id);
		
		//alert(jQuery('#comment_parent').val());
		jQuery.cookie('text_signature', paragraphnumber, { path: '/' , expires: 1} );				
		jQuery.cookie('selected_comment_id', comment_id, { path: '/' , expires: 1} );				
		
		
		
	
		var item = jQuery('.commenticonbox').get(parseInt(jQuery('.commenticonbox').index(this)));
		
		
		jQuery('.textblock').removeClass('selected-textblock');
		jQuery('.commenticonbox').removeClass('selected-paragraph');
		
		//alert('.textblock-' + paragraphnumber);
		
		
		if(paragraphnumber > 0){
			jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');
			jQuery('#textblock-' + paragraphnumber + ' .commenticonbox').addClass('selected-paragraph');
		
			var textblockname = "#textblock-" + paragraphnumber;
			var textblock = jQuery(textblockname);
	
			var left = textblock.position().left;
			top = textblock.position().top;
			
		}			
		var commentbox = jQuery("#commentbox");

		var scrollto = top;
		jQuery('#respond').appendTo(current_comment_id + ' .comment-respond');		
		
		jQuery(window).scrollTo(scrollto, 200);
		jQuery('#commentbox').scrollTo( jQuery(current_comment_id + ' .comment-header'), 0)
	
		document.location.hash = '#' + paragraphnumber;
		
				
		jQuery('.comment .comment-reply').html('reply');
		jQuery(current_comment_id + ' .comment-reply').html('cancel response');


		
	}, function(){

		var paragraphnumber = jQuery('#selected_paragraph_number').attr('value');
		
		jQuery('#comment_parent').val(0);
		jQuery('.comment-reply').html('reply');
		jQuery('#respond').appendTo('#paragraph-block-'+(paragraphnumber));
		
		
	});


});
