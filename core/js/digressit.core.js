var AjaxResult = {};
var grouping_digressit_commentbox_parser;
var userAgent = navigator.userAgent.toLowerCase();
var msie=jQuery.browser.msie;
var msie6=jQuery.browser.msie && jQuery.browser.version=="6.0";
var msie7=jQuery.browser.msie && jQuery.browser.version=="7.0";
var msie8=jQuery.browser.msie && jQuery.browser.version=="8.0";
var safari=jQuery.browser.safari;
var chrome=jQuery.browser.chrome;
var mozilla=jQuery.browser.mozilla;
var iOS = navigator.platform == 'iPad' || navigator.platform == 'iPhone' || navigator.platform == 'iPod';
var zi=10000;
var window_has_focus = true;
var selected_comment_color = '#3d9ddd';
var unselected_comment_color = '#DFE4E4';
var request_time = 0;
var request_time_delay = 500; // ms - adjust as you like


//highlight a block of text
jQuery.fn.highlight = function (str, className){
	return this.each(function (){
		this.innerHTML = this.innerHTML.replace(new RegExp(str, "g"), "<span class=\"" + className + "\">" + str + "</span>");
	});
};
	

// Figure out what browser is being used
jQuery.browser = {
	version: (userAgent.match( /.+(?:rv|it|ra|ie|me)[\/: ]([\d.]+)/ ) || [])[1],
	chrome: /chrome/.test( userAgent ),
	safari: /webkit/.test( userAgent ) && !/chrome/.test( userAgent ),
	opera: /opera/.test( userAgent ),
	msie: /msie/.test( userAgent ) && !/opera/.test( userAgent ),
	mozilla: /mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent )
};


if(jQuery.browser.msie){
	(function(){
		var html5elmeents = "address|article|aside|audio|canvas|command|datalist|details|dialog|figure|figcaption|footer|header|hgroup|keygen|mark|meter|menu|nav|progress|ruby|section|time|video".split('|');	
		for(var i = 0; i < html5elmeents.length; i++){		
			document.createElement(html5elmeents[i]);
		}
	})();
}


jQuery(document).ready(function() {

	// Paragraph embeds
	jQuery('.paragraphembed a').bind('click', function(e){
		e.preventDefault();
		var id = jQuery(this).attr('rel');
		jQuery('#embedcode-' + id).show();
		return false;
	});
	
	jQuery('.closeme').bind('click', function(e){
		e.preventDefault();
		jQuery('.embedcode').hide();
		return false;	
	});

	jQuery('.embedcode').click(function(){
		return 0;
	});
	
	jQuery('.submit, .lightbox-submit').click(function(e){
		if(jQuery(e.target).hasClass('ajax')){
			//return false;
		}
		else{
			jQuery(e.target).addClass('disabled');
		}
	});

	jQuery(window).keyup(function(e){
		var ESC = 27; // Leave comment box
		var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
		if ( key == ESC){
			jQuery(window).closelightbox();		
		}
	});
	
	if(typeof keyboard_navigation != 'undefined' && keyboard_navigation == true){
		jQuery(window).keyup(function(e){
			var UP = 38; // Prev paragraph
			var DOWN = 40; // Next paragraph
			var C = 67; // Enter comment box
			var ESC = 27; // Leave comment box
			var J = 74; // Previous section
			var K = 75; // Next section
			var paragraphnumber;
			var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
			
			// Next/prev paragraph
			if ( key == UP || key == DOWN)
			{
				var selected_paragraph_number = parseInt(jQuery('#selected_paragraph_number').val());
				if(selected_paragraph_number > 0)
				{
					// Nothing to do
				}
				else
				{
					selected_paragraph_number = 1;
				}
				
				if(selected_paragraph_number ){
							
					if(key == UP){
						paragraphnumber = selected_paragraph_number - 1;
					}
					else if(key == DOWN){
						paragraphnumber = selected_paragraph_number + 1;
					}
									
					jQuery('#respond').hide();			
					jQuery('#respond').appendTo('#paragraph-block-'+(paragraphnumber) + ' .toplevel-respond');
					jQuery('#respond').show();			
					jQuery('.comment' ).hide();
					jQuery('.paragraph-' + paragraphnumber ).show();
					//jQuery("#no-comments").hide();				
		
					jQuery('.textblock').removeClass('selected-textblock');
					var commentboxtop = jQuery('#commentbox').position().top;
		
					if(paragraphnumber > 0)
					{
						jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');
		
						var top = parseInt(jQuery('#textblock-' + paragraphnumber).offset().top);
						var scrollto = (top > 200)  ? (top - 100) : 0;
		
						if(iOS){
							jQuery('#commentbox').position_main_elements();						
						}
				
						jQuery(window).scrollTo(scrollto , 100);
		
		
						jQuery('#commentbox').scrollTo('#paragraph-block-'+(paragraphnumber) , 500, {easing:'easeOutBack'});
					}
					
					jQuery('#selected_paragraph_number').val(paragraphnumber);
					document.location.hash = '#' + paragraphnumber;			
				}
			}
			
			// Move to comment field
			else if (key == C)
			{
				jQuery('#comment').focus().keyup(function(e){
					var k = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
					
					// Move out of the comment field
					if (k == ESC)
					{
						jQuery('#comment').blur();
					}
				});
			}
			
			// Previous
			else if (key == J)
			{
				var prev = jQuery('.navigation-previous a').eq(0);
				if (prev.length == 1)
				{
					window.location = prev.attr('href');
				}
			}
			
			// Next
			else if (key == K)
			{
				var next = jQuery('.navigation-next a').eq(0);
				if (next.length == 1)
				{
					window.location = next.attr('href');
				}
			}	
		});
	}

	


	
	jQuery("#mainpage .navigation a").hover(function (e) {
		jQuery('#mainpage .preview').hide();
		var index = jQuery('#mainpage .navigation a').index(this) + 1;
		var item = jQuery('#mainpage .preview').get(index);			
		jQuery(item).show();
	});
	
    jQuery("#mainpage").not('.navigation a').hover(function (e) {
		jQuery('#mainpage .preview').hide();
		jQuery('#mainpage .default').show();
	});






/*** THE FOLLOWING ARE AJAX FORMS WITH DIFFERENT VARIENTS ON SUBMISSIONS. @TODO THIS COULD BE CONSOLIDATED */

	/* #1)  Ajax form */
	jQuery('.ajax').live('click', function(e) {		
		if(jQuery(this).hasClass('disabled') || jQuery(this).hasClass('button-disabled')){
			jQuery(this).css('color', '#DFE4E4');
			return;
		}
		
		var form = this;
		form = jQuery(this).parentsUntil('form').parent();		
		var form_id = jQuery(form).attr('id');
		var function_name = form_id;
		var form_class = jQuery(form).attr('class');
		var fields = {};

		
		jQuery('input[type=button]').attr('disabled', true);
		jQuery('input[type=submit]').attr('disabled', true);
		jQuery('.lightbox-submit').addClass('disabled');
		jQuery('.submit').addClass('disabled');
		
		jQuery('form #' + form_id + ' .loading,' + '#' + form_id + ' .loading-bars, ' + '#' + form_id + ' . loading-bar , #' + form_id + ' .loading-throbber').css('display', 'inline');
		
		jQuery.post( siteurl + "/ajax/" + form_id +'/',	jQuery("#"+form_id).serialize(),
			function( data ) {	
				
				function_name = function_name.replace(/-/g, '_');// + "_ajax_result";

				var dynamic_call = 'typeof(AjaxResult.' + function_name + ') != "undefined"';


				if(eval(dynamic_call)){
					eval('AjaxResult.' + function_name + '(data);');
				}
				else{
					
				}

				jQuery('input[type=button]').attr('disabled', false);
				jQuery('input[type=submit]').attr('disabled', false);
				jQuery('.lightbox-submit').removeClass('disabled');
				jQuery('.submit').removeClass('disabled');
				
				jQuery('.loading, .loading-bars, .loading-bar, .loading-throbber').hide();
				
			}, 'json' );



			
	});
	
	/* #2) Ajax form - after pressing submit button */	
	jQuery('.ajax-auto-submit input').live('keyup', function(e) {
		if(e.keyCode == 13 && jQuery(this).val().length > 0){

			//return;
		}
		else{
			return;
		}

		if(request_time) {
			clearTimeout(request_time)
		}
		
		request_time = setTimeout(function(obj){
			if(jQuery(obj).hasClass('disabled') || jQuery(this).hasClass('button-disabled')){
				jQuery(obj).css('color', '#DFE4E4');
				return true;
			}
		
			var form = obj;
			form = jQuery(form).parentsUntil('form').parent();		

	
			var form_id = jQuery(form).attr('id');
			var form_class = jQuery(form).attr('class');		
			var function_name = form_id;
			var function_parameters = jQuery("#"+form_id).serialize();

			jQuery.post( siteurl + "/ajax/" + function_name +'/',	function_parameters,
				function( data ) {					
					function_name = function_name.replace(/-/g, '_');// + "_ajax_result";

					var dynamic_call = 'typeof(AjaxResult.' + function_name + ') != "undefined"';
					if(eval(dynamic_call)){
						eval('AjaxResult.' + function_name + '(data);');
					}
					else{

					}
				}, 'json' );
		}, request_time_delay, this);
	});
	

	/* #3) Ajax form - after pressing submit button */	
	jQuery('.ajax-live').live('keyup', function(e) {

		if(request_time) {
			clearTimeout(request_time)
		}
		
		request_time = setTimeout(function(obj){
			if(jQuery(obj).hasClass('disabled') || jQuery(this).hasClass('button-disabled')){
				jQuery(obj).css('color', '#DFE4E4');
				return true;
			}
			
			if(!jQuery(obj).attr('class').toString().length){
				return
			}
			var ajax_simple_classes = jQuery(obj).attr('class').split(' ');
			for(var i = 0; i < ajax_simple_classes.length; i++){

				if(ajax_simple_classes[i] == 'ajax-live'){
					function_name = ajax_simple_classes[i+1];
					break;				
				}
			}

			var form_id = jQuery(obj).attr('id');
			jQuery('#' + form_id + ' .loading,' + '#' + form_id + ' .loading-bars, ' + '#' + form_id + ' . loading-bar , #' + form_id + ' .loading-throbber').css('display', 'inline');

			var function_parameters = {'value' :jQuery(obj).attr('value')};
			jQuery.post( siteurl + "/ajax/" + function_name +'/',	function_parameters,
				function( data ) {					
					function_name = function_name.replace(/-/g, '_');// + "_ajax_result";

					var dynamic_call = 'typeof(AjaxResult.' + function_name + ') != "undefined"';
					if(eval(dynamic_call)){
						eval('AjaxResult.' + function_name + '(data);');
					}
					else{

					}
					
					jQuery('.loading, .loading-bars, .loading-bar, .loading-throbber').css('display', 'none');
					
				}, 'json' );
				
		}, request_time_delay, this);
	});
	
	
	/* #4) Clicking a span or div */	
	jQuery('.ajax-simple').live('click', function (e) {
		if(jQuery(this).hasClass('disabled') || jQuery(this).hasClass('button-disabled')){
			jQuery(this).css('color', '#DFE4E4');
			return true;
		}
		
		var ajax_simple_classes = jQuery(this).attr('class').split(' ');

		for(var i = 0; i < ajax_simple_classes.length; i++){
			if(ajax_simple_classes[i] == 'ajax-simple'){
				function_name = ajax_simple_classes[i+1];
				break;				
			}
		}

		var function_parameters = parseGetVariables( jQuery(this).attr('value'));

//		console.log('function_parameters' + function_parameters  + "\n")
		jQuery(this).css('cursor', 'wait');
		jQuery.post( siteurl + "/ajax/" + function_name +'/',	function_parameters,
			function( data ) {					
				function_name = function_name.replace(/-/g, '_');// + "_ajax_result";

				var dynamic_call = 'typeof(AjaxResult.' + function_name + ') != "undefined"';
				if(eval(dynamic_call)){
					eval('AjaxResult.' + function_name + '(data);');
				}
				else{
					
				}
				
				jQuery(this).css('cursor', 'auto');
				
				
			}, 'json' );
	});
	
	
	/* #5) Ajax form - after pressing submit button */	
	
	//same as above, just a click event. hack fix for tabs not calling ajax.
	jQuery('.ajax-simple-click').click(function (e) {
		if(jQuery(this).hasClass('disabled') || jQuery(this).hasClass('button-disabled')){
			jQuery(this).css('color', '#DFE4E4');
			return true;
		}
		
		var ajax_simple_classes = jQuery(this).attr('class').split(' ');

		for(var i = 0; i < ajax_simple_classes.length; i++){
			if(ajax_simple_classes[i] == 'ajax-simple-click'){
				function_name = ajax_simple_classes[i+1];
				break;				
			}
		}

		var function_parameters = parseGetVariables( jQuery(this).attr('value'));
		jQuery(this).css('cursor', 'wait');
		jQuery.post( siteurl + "/ajax/" + function_name +'/',	function_parameters,
			function( data ) {					
				function_name = function_name.replace(/-/g, '_');// + "_ajax_result";

				var dynamic_call = 'typeof(AjaxResult.' + function_name + ') != "undefined"';
				if(eval(dynamic_call)){
					eval('AjaxResult.' + function_name + '(data);');
				}
				else{
					
				}
				
				jQuery(this).css('cursor', 'auto');
				
				
			}, 'json' );
	});

	
	jQuery('.ajax-auto-update').live('change', function(e) {
		if(jQuery(this).hasClass('disabled') || jQuery(this).hasClass('button-disabled')){
			jQuery(this).css('color', '#DFE4E4');
			return true;
		}
		
		var ajax_simple_classes = jQuery(this).attr('class').split(' ');

		for(var i = 0; i < ajax_simple_classes.length; i++){
			if(ajax_simple_classes[i] == 'ajax-auto-update'){
				function_name = ajax_simple_classes[i+1];
				break;				
			}
		}

		var var_value;
		
		if(jQuery(this).attr('type') == 'checkbox'){
			if(jQuery(this).attr("checked")){
				var_value = jQuery(this).attr('value');				
			}
			else{
				var_value = false;
			}
		}
		else{
			 var_value = jQuery(this).attr('value');
		}
		
		var name_values = jQuery(this).attr('name') + '=' + var_value;
		var function_parameters = parseGetVariables(name_values);
		
		jQuery('.' + function_name + '  + .loading-throbber' + ', .' + function_name + '  + .loading-bars').css('display','inline');
		
		jQuery.post( siteurl + "/ajax/" + function_name +'/',	function_parameters,
			function( data ) {					
				function_name = function_name.replace(/-/g, '_');// + "_ajax_result";

				jQuery('.loading, .loading-bars, .loading-bar, .loading-throbber').hide();
				var dynamic_call = 'typeof(AjaxResult.' + function_name + ') != "undefined"';
				if(eval(dynamic_call)){
					eval('AjaxResult.' + function_name + '(data);');
				}
				else{
					
				}
				
				
				
			}, 'json' );
	});
	
		
	function ajax_callback(function_name, data) {
		window[function_name](data);
	}
	
	function call_method (func){
	    this[func].apply(this, Array.prototype.slice.call(arguments, 1));
	}
	

	if(typeof digressit_enabled != 'undefined' && digressit_enabled){
		jQuery('#commentbox').position_main_elements();
	}
	
    jQuery(window).scroll(function () { 
	
		//this should not fire every single time! do proper checks to help performance
		if(digressit_enabled){
			jQuery('#commentbox').position_main_elements();
			//console.log('scroll');
		}
		
    });




	jQuery(window).resize(function(){
		if(digressit_enabled){
			jQuery('#commentbox').position_main_elements();			
			//console.log('resize');
		}
	});
	
	
	


	function parseGetVariables(variables) {
		var var_list = {};
		var vars = variables.split("&");
		
		for (var i=0;i<vars.length;i++) {
			var pair = vars[i].split("=");
			var_list[pair[0]] = pair[1];
		}

		return var_list;
	}
	
	
	//http://www.idealog.us/2006/06/javascript_to_p.html
	function getQueryVariable(variable) {
		var query = window.location.search.substring(1);
		var vars = query.split("&");
		for (var i=0;i<vars.length;i++) {
			var pair = vars[i].split("=");
			if (pair[0] == variable) {
				return pair[1];
			}
		}
		return false;
	}
	
    jQuery(".commentarea").hover(
      function () {
		var pos = jQuery('.commentarea').index(this);
		//jQuery('.paragraph_feed').eq(pos).css('visibility', 'visible');
      }, 
      function () {
		var pos = jQuery('.commentarea').index(this);
		//jQuery('.paragraph_feed').eq(pos).css('visibility', 'hidden');
      }
    );


    jQuery(".paragraph_feed").click(function () {
		var paragraph = jQuery('.paragraph_feed').index(this);
		window.location.href = '/feed/paragraphcomments/'+post_name+','+paragraph;
      }
    );

	jQuery(".embed-link").click(function (e) {
		var id = jQuery(this).attr('id').substr(11);
		var format = jQuery(this).attr('id').substr(6, 4);

		if(format == 'obje'){
			id = jQuery(this).attr('id').substr(13);
			var data = '<object style="width: 100%;" onload="this.style.height = (this.contentDocument.body.offsetHeight + 40) + \'px\'; this.style.width = (this.contentDocument.body.offsetWidth + 40) + \'px\'" class="digressit-paragraph-embed" data="' + wp_path + '?p='+ post_ID +'&digressit-embed='+ id +'"></object><a href="' + window.location.href + '#'+id+'">@</a>';
			jQuery("#textarea-embed-" + id).text(data);			
		}
		else{		
			jQuery.get(wp_path + '?p=' + post_ID +'&format=' + format + '&digressit-embed=' + id, function(data){
				jQuery("#textarea-embed-" + id).text(data);
			});
		}
	});	
	
	


	if (document.location.hash.length) {
		var hashtag = document.location.hash.substr(1);
		if(isNaN(hashtag) && hashtag.search('comment-') != 0){
			var lightbox = 'lightbox-' + hashtag;
			jQuery('body').openlightbox(lightbox);
		}
	}	


	if (jQuery('.lightbox-auto-load').length) {
		var lightbox = '#'+jQuery('.lightbox-auto-load:first').attr('id');
		jQuery("body").closelightbox();

		jQuery('body').openlightbox(lightbox);
	}
	
	
	jQuery("#search_context").change(function (e) {		
		jQuery("#searchform").attr('action', jQuery("#search_context option:selected").val());
	});


	
	/*
	jQuery('.lightbox').click(function(){
		var lightbox_names = jQuery(this).attr('class').split(' ');
		
		console.log(lightbox_names[1]);
		jQuery('body').openlightbox(lightbox_names[1]);

	});
	*/

    jQuery(".lightbox").live('click', function(e){

		if(jQuery(e.target).hasClass('button-disabled') || jQuery(e.target).hasClass('disabled')){
			return false;
		}

		var lightbox_name = jQuery(this).attr('class').split(' ');
	
		var lightbox, i;
		for(i = 0; i < lightbox_name.length; i++){
			if(lightbox_name[i] == 'lightbox'){
				lightbox = lightbox_name[i+1];
				break;				
			}
		}	
		jQuery('body').openlightbox(lightbox);
	
	});
	
	
	jQuery('.close').live('click', function(e){
		jQuery(this).parent().hide();		
		jQuery('#block-access').hide();
	});
	
	jQuery(".insert-link").live('click', function(e){
		var name = jQuery("#link_name").val();
		var link = jQuery("#link_url").val();
		jQuery("#comment").val(jQuery("#comment").val() + '<a href="'+link+'">'+name+'</a>');
		jQuery("body").closelightbox();
		
	});
	
	
	jQuery(".lightbox-content input[type=text], .lightbox-content input[type=password]").live('keyup', function(e){

		if (e.keyCode == '13') {
			if(jQuery(this).hasClass('ajax')){
		  		/* UNDO COMMENT: */
				//jQuery(this).add('.lightbox-submit').click();
			}
			else{
				jQuery(e.target).parentsUntil('form').parent().submit();		
				//alert(jQuery(e.target).parentsUntil('form').parent().attr('id'));
		  		/* UNDO COMMENT: */
				//jQuery(this).submit();	
			}
		}
	});



    jQuery(".lightbox-close, .lightbox-submit-close").live('click', function(e){
		jQuery('body').closelightbox();
	});
	
	/*
    jQuery(".lightbox-submit-close").click(function (e) {

		jQuery('body').closelightbox();
	});
	*/
	
	
    jQuery(".lightbox-submit").live('click', function(e){

		if(jQuery(this).hasClass('ajax')){
		}
		else{
	  		jQuery(this).parent().submit();				
		}

	});



    jQuery(".lightbox").hover(function (e) {
		jQuery(this).css('cursor', 'pointer');
	},	
	function (e) {
		jQuery(this).css('cursor', 'auto');
	});	

    jQuery(".lightbox-images").click(function (e) {
		jQuery('#lightbox-images').css('left', '10%');
		jQuery('#lightbox-images').css('top','5%');
		
		jQuery('#lightbox-images .ribbon-title').html(jQuery(this).attr('title'));

		var imagesrc = jQuery(this).attr('src').replace(siteurl, '');
		
		jQuery('#lightbox-images .large-lightbox-image').empty();
						
		jQuery.post( baseurl + "/ajax/lightbox-image/",		
			{ blog_id: blog_ID, imagesrc: imagesrc},
			function( data ) {				
				//console.log(data);
				jQuery('#lightbox-images .large-lightbox-image').html('<img src="' +data.message + '">');			
				
			}, 'json' );	
	});
	
	
	
	jQuery(".lightbubble").click(function (e) {


		if(jQuery(e.target).hasClass('button-disabled') || jQuery(e.target).hasClass('disabled')){
			return false;
		}


		var target = e.target;
		var lightbubble_name = jQuery(target).attr('class').split(' ');
	
		var lightbubble, i;
		for(i = 0; i < lightbubble_name.length; i++){
		
			if(lightbubble_name[i] == 'lightbubble'){
				lightbubble = '#' + lightbubble_name[i+1];
				break;				
			}
		}

		jQuery(lightbubble).appendTo(jQuery(this));
		jQuery(lightbubble).show();
	
	});		
	

    jQuery(".required").change(function (e) {

		var form =jQuery(this).parentsUntil('form').parent();		

		
		var form_id = jQuery(form).attr('id');

		jQuery('#' + form_id + ' .lightbox-submit').removeClass('button-disabled');			
		
		jQuery('#' + form_id + ' .required').each(function(e){
			
			if( (
					(
						jQuery(this).attr('type') == 'text' && jQuery(this).val().length == 0
					) 
					|| 
					(
						( jQuery(this).attr('type') == 'radio' || jQuery(this).attr('type') == 'checkbox')  
						&& 
						( jQuery("input[name='"+jQuery(this).attr('name')+"']").is(':checked') == false )
					)
				)
			  )
			{
				jQuery('#' + form_id + ' .lightbox-submit').addClass('button-disabled');			
			}
			
		})
		

	});


	AjaxResult.add_comment = function(data) {
		var result_id = parseInt(data.message.comment_ID);



		if(data.status == 0){
			jQuery('body').displayerrorslightbox(data);
			return;
		}
		
		console.log(jQuery('#selected_paragraph_number').val());
		var selected_paragraph_number = parseInt(  jQuery('#selected_paragraph_number').val()  );

		var comment_parent  = data.message.comment_parent;

		var comment_id = 'comment-' +  blog_ID + '-' + result_id;
		var parent_id = 'comment-' +  blog_ID + '-' + data.message.comment_parent;
		var depth = 'depth-1';
		if(data.message.comment_parent > 0){
			depth = 'depth-2';
		}

		var new_comment = data.message.comment_response;

		/* responding to parent */
		if(comment_parent > 0){
			//we are grouping comments
			if(jQuery('#paragraph-block-' + selected_paragraph_number).length){
				jQuery('#respond').appendTo('#paragraph-block-' + selected_paragraph_number + ' .toplevel-respond');			
				jQuery('#paragraph-block-' + selected_paragraph_number).append(new_comment);
				//jQuery('#commentbox').scrollTo('#'+comment_id , 200);
				jQuery('.comment-reply').html('reply');
				jQuery('#'+comment_id).fadeIn("#"+comment_id);
				jQuery('#commentbox').scrollTo('#'+comment_id , 500, {easing:'easeOutBack'});
				
			}
			else{
				if( jQuery('#' + parent_id).next().hasClass('children') ){
					jQuery('#' + parent_id + ' + .children').prepend(new_comment);
					
					jQuery('#'+comment_id).fadeIn("#"+comment_id);
				}
				else{
					jQuery('#' + parent_id).after('<ul class="children">' + new_comment + '</ul>');					
					jQuery('#'+comment_id).fadeIn("#"+comment_id);
				}

			}
		}
		/* new thread */
		else{
			//we are grouping comments
			if(jQuery('#paragraph-block-' + selected_paragraph_number).length){
				jQuery(new_comment).appendTo('#'+ 'paragraph-block-' + selected_paragraph_number);						
				jQuery('#'+comment_id).fadeIn("#"+comment_id);
				jQuery('#commentbox').scrollTo('#'+comment_id , 500, {easing:'easeOutBack'});
			}
			else{
				jQuery('.commentlist').prepend(new_comment);			
				jQuery('#'+comment_id).fadeIn("#"+comment_id);
			}

		}

		//var current_count = parseInt(jQuery(jQuery('#content .commentcount').get((selected_paragraph_number ))).html());
		jQuery(jQuery('#content .commentcount').get((selected_paragraph_number -1 ))).html(data.message.paragraph_comment_count);
		jQuery(jQuery('#content .commentcount').get((selected_paragraph_number -1))).fadeIn('slow');


		jQuery(jQuery('#commentbox .commentcount').get((selected_paragraph_number))).html(data.message.paragraph_comment_count);
		jQuery(jQuery('#commentbox .commentcount').get((selected_paragraph_number))).fadeIn('slow');

		jQuery(jQuery('#digress-it-list-posts .sidebar-current .commentcount').get(0)).html(data.message.comment_count);
		jQuery(jQuery('#digress-it-list-posts .sidebar-current .commentcount').get(0)).fadeIn('slow');

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


	jQuery('.comment').hover(function (e) {

		if(jQuery('body').hasClass('digressit-enabled')){
			return;
		}

		var index = jQuery('.comment').index(this);		
		if(jQuery('.comment-goto').length){
			var item = jQuery('.comment-goto').get(index);
			if(item){
				jQuery(item).show();			
			}
		}

	},	function (e) {
		if(jQuery('body').hasClass('digressit-enabled')){
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




		if(jQuery('body').hasClass('digressit-enabled')){
			var selected_blog_id = jQuery.cookie('selected_blog_id');				
		}

		if(!jQuery(target).parents().hasClass('comment-respond') && !jQuery(target).hasClass('comment-reply') && !jQuery('body').hasClass('page-template-moderator-php')){
			//commentbox_open_state();			
		}



		if(jQuery('body').hasClass('digressit-enabled') && jQuery('.comment-reply').length){
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

	jQuery("#comment").focus(function (e) {
		if( jQuery(this).val() == 'Click here add a new comment...'){
			jQuery(this).val('');
			jQuery('#submit-comment').removeClass('disabled');			
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






	
	function isNumber(n) {
	  return !isNaN(parseFloat(n)) && isFinite(n);
	}

	grouping_digressit_commentbox_parser = function(data){

		jQuery('.textblock').each(function(i){
			var paragraphnumber = (i == 0) ? '&nbsp;'  : i;
			var commentlabel = (i == 0) ? ' general comment'  : ' comment';
			var commentcount = jQuery('.paragraph-' + (i)).length;
			
			commentlabel = (commentcount == 1) ? commentlabel  : commentlabel + 's';
			
			jQuery("#commentwindow").append('<div class="paragraph-block" id="paragraph-block-'+(i)+'"><div class="paragraph-block-button"><span class="paragraph-label">'+(paragraphnumber)+'</span>&nbsp; <span class="commentcount">'+commentcount+'</span> '+commentlabel+'</div><div class="toplevel-respond"></div></div>');
			
			jQuery('.paragraph-' + (i)).appendTo('#paragraph-block-'+(i));				
			
		});
		
		if(jQuery('.textblock').length > 0){

			var i = jQuery('.textblock').length;
			var paragraphnumber = (i == 0) ? '&nbsp;'  : i;
			var commentlabel = (i == 0) ? ' general comment'  : ' comment';
			var commentcount = jQuery('.paragraph-' + (i)).length;
			
			commentlabel = (commentcount == 1) ? commentlabel  : commentlabel + 's';
			
			jQuery("#commentwindow").append('<div class="paragraph-block" id="paragraph-block-'+(i)+'"><div class="paragraph-block-button"><span class="paragraph-label">'+(paragraphnumber)+'</span>&nbsp; <span class="commentcount">'+commentcount+'</span> '+commentlabel+'</div><div class="toplevel-respond"></div></div>');
			
			jQuery('.paragraph-' + (i)).appendTo('#paragraph-block-'+(i));				

		}
	}


	if(typeof(commentbox_function) !== 'undefined'){
		var dynamic_call = 'typeof(' + commentbox_function + ') != "undefined"';
		if(eval(dynamic_call)){
			eval(commentbox_function + '();');
		}
	}


	var comment_linked;

	jQuery('.comment').click(function(e){
		var index = jQuery('.comment').index(this);

		//comment-id
		var selected_blog_id = jQuery(jQuery('.comment .comment-blog-id').get(index)).attr('value');
		var selected_comment_id = jQuery(jQuery('.comment .comment-id').get(index)).attr('value');

		jQuery.cookie('selected_comment_id', null, { path: request_uri, expires: 1} );
		jQuery.cookie('selected_blog_id', null, { path: request_uri + '/', expires: 1} );

		jQuery.cookie('selected_comment_id', selected_comment_id, { path: '/', expires: 1} );
		jQuery.cookie('selected_blog_id', selected_blog_id, { path: '/', expires: 1} );
	});

	function expand_comment_area(item, paragraphnumber){
		jQuery('.textblock').removeClass('selected-textblock');
		jQuery('.commenticonbox').removeClass('selected-paragraph');
		jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');
		jQuery('#textblock-' + paragraphnumber + ' .commenticonbox').addClass('selected-paragraph');
		jQuery('.comment').removeClass('selected');	
		jQuery('#selected_paragraph_number').val(paragraphnumber);
		//jQuery("#no-comments").hide();
		
		var no_comments = true;

		jQuery(".comment").hide();
		jQuery("#respond").show();
		jQuery('textblock-' +paragraphnumber).addClass('selected-textblock');	

		var selectedparagraph  = ".paragraph-" + paragraphnumber;
		
		if(jQuery(selectedparagraph).length){
			jQuery(selectedparagraph).show();
		}
		/*else{
			if(jQuery('.comment').length){
				jQuery("#no-comments").show();
			}			
		}*/
	}

	
	/****************************************************
	*	only if we are grouping the paragraphs 
	****************************************************/

	if(jQuery('.paragraph-block').length){

		//* this only happens when we are using the standard theme */
		if (isNumber(document.location.hash.substr(1))) {
			var paragraphnumber = document.location.hash.substr(1);
			if(paragraphnumber > jQuery('.textblock').length){
				return;
			}
			jQuery('.paragraph-'+(paragraphnumber)).show();			
			jQuery('#respond').appendTo('#paragraph-block-'+(paragraphnumber));
		}

		jQuery("#cancel-response").click(function (e) {
			//jQuery('#comment_parent').val(0);
			jQuery('#comment').val('Click here add a new comment...');
			jQuery('#submit-comment').addClass('disabled');
			
		});


		jQuery("#menu ul li").click(function (e) {
			jQuery('#comment_parent').val(0);
			jQuery('#comment').val('Click here add a new comment...');

			jQuery('#submit-comment').hide();
			jQuery('#cancel-response').hide();

			jQuery('.textblock').removeClass('selected-textblock');
			jQuery('.comment').hide();
			jQuery('#respond').hide();						
			jQuery('#selected_paragraph_number').val(0);
		});
		//jQuery('<li class="page_item"><input class="live-post-search" type="text" value="Search"></li>').appendTo('.menu ul');
		
		
		jQuery('.textblock').click(function(e){

			//paragraphembed
			

			if(jQuery('.paragraphembed').length){
				if(jQuery(e.target)[0].nodeName == "TEXTAREA" || jQuery(e.target)[0].nodeName == "INPUT"){					
					jQuery(e.target)[0].focus();
					jQuery(e.target)[0].select();
					jQuery.copy(jQuery(jQuery(e.target)[0]).text());
					jQuery('.text-copied').fadeIn('slow');
					jQuery('.text-copied').fadeOut('slow');

					return;
				}
				else if(jQuery(e.target).hasClass('embedcode')){
					return;
				}
			}
			
			if(open_if_linked_in_paragraph(e)){
				return;
			}
			var paragraphnumber = parseInt(jQuery('.textblock').index(this)) +1 ;
	
	
			if(parseInt(jQuery('#selected_paragraph_number').val()) == paragraphnumber){

				/* PARAGRAPH BLOCKS - UNSELECTED */
				if(jQuery('.paragraph-block').length){
					jQuery('.textblock').removeClass('selected-textblock');
					jQuery('.comment').hide();
					jQuery('#respond').hide();
					//jQuery("#no-comments").hide();				
					jQuery('#selected_paragraph_number').val(0);
				}
				/* ALL COMMENTS - UNSELECTED */
				else{
					if(jQuery('.comment' ).length){
						//jQuery("#no-comments").hide();				
						jQuery(".comment").show();				
					}
					else{
						//jQuery("#no-comments").show();
					}
					jQuery('.textblock').removeClass('selected-textblock');
					jQuery('#selected_paragraph_number').val(0);
					jQuery('#respond').appendTo(jQuery('#toplevel-commentbox'));						
				}	
			}
	
			else{
		
				/* PARAGRAPH BLOCKS - SELECTED */
				if(jQuery('.paragraph-block').length){
					jQuery('#respond').hide();			

					jQuery('#respond').appendTo('#paragraph-block-'+(paragraphnumber) + ' .toplevel-respond');
					jQuery('#respond').show();			
					jQuery('.comment' ).hide();
					jQuery('.paragraph-' + paragraphnumber ).show();
					//jQuery("#no-comments").hide();				

					jQuery('.textblock').removeClass('selected-textblock');
					var commentboxtop = jQuery('#commentbox').position().top;

					if(paragraphnumber > 0){
						jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');

						var top = parseInt(jQuery('#textblock-' + paragraphnumber).offset().top);
						var scrollto = (top > 200)  ? (top - 100) : 0;

						if(iOS){
							jQuery('#commentbox').position_main_elements();						
						}
				
						jQuery(window).scrollTo(scrollto , 100);


						jQuery('#commentbox').scrollTo('#paragraph-block-'+(paragraphnumber) , 500, {easing:'easeOutBack'});
					}
					jQuery('#selected_paragraph_number').val(paragraphnumber);
		
					document.location.hash = '#' + paragraphnumber;

				}
				else{
					/* ALL COMMENTS - SELECTED */

					jQuery('#respond').hide();			
					var paragraphnumber = parseInt(jQuery('.textblock').index(this)) +1 ;

					jQuery('#respond').appendTo('#toplevel-commentbox');
					jQuery('#respond').show();			
					jQuery('.comment' ).hide();
					jQuery('.paragraph-' + paragraphnumber ).show();


					jQuery('#submit-comment').removeClass('disabled');

					jQuery('.textblock').removeClass('selected-textblock');
					jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');

					jQuery('#selected_paragraph_number').val(paragraphnumber);


					/*
					if(jQuery('.paragraph-' + paragraphnumber ).length){
						jQuery("#no-comments").hide();				
					}
					else{
						jQuery("#no-comments").show();
					}
					*/

					var top = jQuery('#textblock-' + paragraphnumber).offset().top;
					var scrollto = (top > 200)  ? (top - 35) : 0;

					jQuery(window).scrollTo(scrollto , 100);
					document.location.hash = '#' + paragraphnumber;
				}
			}

		});
	}


		

	jQuery('.paragraph-block-button').toggle(function(e){
		jQuery('.comment').hide();

		var paragraphnumber = parseInt(jQuery('.paragraph-block-button').index(this));
		jQuery('#selected_paragraph_number').val(paragraphnumber);
		jQuery('.paragraph-' + paragraphnumber).show();
		jQuery('#respond').appendTo('#paragraph-block-'+(paragraphnumber) + ' .toplevel-respond');
		jQuery('#respond').show();			

		jQuery('.textblock').removeClass('selected-textblock');

		if(paragraphnumber > 0){

			var top = parseInt(jQuery('#textblock-' + paragraphnumber).offset().top);
			jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');
		
			var scrollto = (top > 200)  ? (top - 100) : 0;
			jQuery(window).scrollTo(scrollto , 200);
			jQuery('#commentbox').scrollTo('#paragraph-block-'+(paragraphnumber) , 500, {easing:'easeOutBack'});
		}
		
	}, function(e){
		jQuery('.comment').hide();
		jQuery('#respond').hide();
		jQuery('.textblock').removeClass('selected-textblock');
		jQuery('#selected_paragraph_number').val(0);
	});



	function open_if_linked_in_paragraph(e){
		
		if(jQuery(e.target).attr('target') && jQuery(e.target).attr('href')){
			window.open(jQuery(e.target).attr('href').toString());				
			return 1;
		}
		else if(jQuery(e.target).attr('href')){
			window.location = jQuery(e.target).attr('href').toString();
			return 1;
		}
		else if(jQuery(e.target).parent().attr('href')){

			if(jQuery(e.target).parent().attr('target') && jQuery(e.target).parent().attr('href')){
				window.open(jQuery(e.target).parent().attr('href').toString());								
			}
			else{
				window.location = jQuery(e.target).parent().attr('href').toString();				
			}

			return 1;
		}
		
		return 0;
	}

	if ( document.location.hash.substr(1, 7) == 'comment') {
		var commentname = document.location.hash.substr(1);

		var comment_info = commentname.split('-');

		if(comment_info.length == 2){
			commentname = 'comment-' + blog_ID + '-'+ comment_info.pop(); 
		}
		
		var paragraphnumber = jQuery(jQuery('#'+commentname + ' .comment-paragraph-number').get(0)).attr('value');
		
		jQuery('#respond').appendTo('#paragraph-block-'+(paragraphnumber) + ' .toplevel-respond');
		jQuery('#respond').show();
		jQuery('.comment').hide();
		jQuery('.paragraph-' + paragraphnumber).show();
		
		jQuery('#selected_paragraph_number').attr('value', paragraphnumber );
		
		
		/*
		if(jQuery('.paragraph-' + paragraphnumber).length == 0){
			jQuery('#no-comments').show();			
		}
		else{
			jQuery('#no-comments').hide();
		}
		*/
		
				
		jQuery('#commentbox').scrollTo('#'+commentname , 500);
		
		if(paragraphnumber > 0){
			var item = jQuery('.commenticonbox').get((paragraphnumber));
			var top = parseInt(jQuery('#textblock-' + paragraphnumber).offset().top);
			jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');

			var scrollto = (top > 200)  ? (top - 100) : 0;
			jQuery(window).scrollTo(scrollto , 500);
		}

	}
	else if ( document.location.hash.substr(1, 13) == 'search-result') {
		jQuery(window).scrollTo( jQuery('.search-result:first'), 1000);
	}
	else if (isNumber(document.location.hash.substr(1))) {
		var paragraphnumber = document.location.hash.substr(1);
		var scrollto;
		if(paragraphnumber > jQuery('.textblock').length){
			return;
		}
		
		if(paragraphnumber > 0){
		
			var item = jQuery('.commenticonbox').get((paragraphnumber));
			var top = parseInt(jQuery('#textblock-' + paragraphnumber).offset().top);

			jQuery('#respond').appendTo('#paragraph-block-'+(paragraphnumber) + ' .toplevel-respond');
			jQuery('#respond').show();
			jQuery('.comment').hide();
			jQuery('.paragraph-' + paragraphnumber).show();
			jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');
			//jQuery('#selected_paragraph_number').attr('value', paragraphnumber );
			jQuery('#selected_paragraph_number').val(paragraphnumber);
			

			/*
			if(jQuery('.paragraph-' + paragraphnumber).length == 0){
				jQuery('#no-comments').show();			
			}
			else{
			}
			jQuery('#no-comments').hide();
			*/
			
		
			scrollto = (top > 200)  ? (top - 100) : 0;
		
			if(jQuery('#paragraph-block-' + paragraphnumber).length){
				jQuery('#commentbox').scrollTo('#paragraph-block-'+(paragraphnumber) , 500);
			}
			
			if(iOS){
				jQuery(window).scrollTo(scrollto , 0);
				jQuery('#commentbox').scrollTo('#paragraph-block-'+(paragraphnumber) , 1000, {easing:'easeOutBack'});				
				jQuery('#commentbox').position_main_elements();						
			}
			else{
				jQuery('#commentbox').scrollTo('#paragraph-block-'+(paragraphnumber) , 1000, {easing:'easeOutBack'});				
				jQuery(window).scrollTo(scrollto  , 500);
				
			}
			
		}

		
	}
	else{
		
		/*
		if( parseInt(jQuery('.comment').length) == 0){
			jQuery("#no-comments").show();			
		}
		else{
			jQuery("#no-comments").hide();	
		}
		*/
	}



	AjaxResult.live_content_search = function(data) {
		jQuery('#live-content-search-result').empty();
		jQuery('#live-content-search-result').html(data.message);
		jQuery('#live-content-search-result').fadeIn();
	}
	
	
	AjaxResult.live_comment_search = function(data) {
		jQuery('#live-comment-search-result').empty();
		jQuery('#live-comment-search-result').html(data.message);
		jQuery('#live-comment-search-result').fadeIn();
	}
	
	
	
	
	/* search content */	
	
	jQuery('#live-content-search').focus(function(){
		if(jQuery('#live-content-search').val() == 'Search Content'){
			jQuery('#live-content-search').val('');
		}
	});
	jQuery('#live-content-search').blur(function(){
		if(jQuery('#live-content-search').val() == ''){
			jQuery('#live-content-search').val('Search Content');
		}
	});
	
	
	/* search comments */	
	jQuery('#live-comment-search').focus(function(){
		if(jQuery('#live-comment-search').val() == 'Search Comments'){
			jQuery('#live-comment-search').val('');
		}
	});
	jQuery('#live-comment-search').blur(function(){
		if(jQuery('#live-comment-search').val() == ''){
			jQuery('#live-comment-search').val('Search Comments');
		}
	});


	

	jQuery('body').click(function (e) {
		if(!jQuery(e.target).hasClass('ajax-live')){
				jQuery('#live-content-search-result').hide();
				jQuery('#live-comment-search-result').hide();
		}
		
	});

	//jQuery.cookie('text_signature', null, { path: '/' , expires: 1} );				
	if(jQuery("#dynamic-sidebar").hasClass('sidebar-widget-auto-hide')){
		if(jQuery("#dynamic-sidebar").hasClass('sidebar-widget-position-right')){
			jQuery("#dynamic-sidebar").hover(function (e) {
				var t = setTimeout(function() {
					jQuery('#dynamic-sidebar').animate({ 
						right: "0px"
					}, 100 ); }, 200);
				jQuery(this).data('timeout', t);
			},function () {
			    clearTimeout(jQuery(this).data('timeout'));
				jQuery('#dynamic-sidebar').animate({ 
					right: "-260px"
				}, 100 );
			});
		}
		else{
			jQuery("#dynamic-sidebar").hover(function (e) {
				var t = setTimeout(function() {
					jQuery('#dynamic-sidebar').animate({ 
						left: "0px"
					}, 200 ); }, 300);
				jQuery(this).data('timeout', t);
			},function () {
			    clearTimeout(jQuery(this).data('timeout'));
				jQuery('#dynamic-sidebar').animate({ 
					left: "-260px"
				}, 100 );
			});
			
		}
	}


	jQuery('.comment-reply').click(function (e) {

		var top = 0;
		var comment_id = jQuery(this).attr('value');
		var current_comment_id = '#comment-'+ blog_ID +'-'+comment_id;		
		var paragraphnumber = jQuery(current_comment_id + ' .comment-paragraph-number').attr('value');
		var comment_id = jQuery(current_comment_id + ' .comment-id').attr('value');
		var blog_id = jQuery(current_comment_id + ' .comment-blog-id').attr('value');

		var selected_paragraphnumber = jQuery('#selected_paragraph_number').attr('value');
		
		

		if(jQuery('#comment_parent').val() == 0){


			jQuery('#selected_paragraph_number').attr('value', paragraphnumber);
			jQuery('#comment_parent').val(comment_id);

			jQuery.cookie('text_signature', paragraphnumber, { path: '/' , expires: 1} );				
			jQuery.cookie('selected_comment_id', comment_id, { path: '/' , expires: 1} );				

			var item = jQuery('.commenticonbox').get(parseInt(jQuery('.commenticonbox').index(this)));

			jQuery('.textblock').removeClass('selected-textblock');
			jQuery('.commenticonbox').removeClass('selected-paragraph');

			if(paragraphnumber > 0){
				jQuery('#textblock-' + paragraphnumber).addClass('selected-textblock');
				jQuery('#textblock-' + paragraphnumber + ' .commenticonbox').addClass('selected-paragraph');

				var textblockname = "#textblock-" + paragraphnumber;
				var textblock = jQuery(textblockname);

				var left = textblock.position().left;
				top = textblock.position().top;

			}			
			var commentbox = jQuery("#commentbox");

			var scrollto = (top - 100);
			jQuery('#respond').appendTo(current_comment_id + ' .comment-respond');		

			jQuery(window).scrollTo(scrollto, 200);
		
			jQuery('#commentbox').scrollTo( current_comment_id , 500, {easing:'easeOutBack'});

			document.location.hash = '#' + paragraphnumber;


			jQuery('.comment .comment-reply').html('reply');
			jQuery(current_comment_id + ' .comment-reply').html('cancel response');

			jQuery(this).addClass('cancel-response');
		}
		else{


			jQuery('#comment_parent').val(0);
			jQuery('.comment-reply').html('reply');
			if(jQuery('.paragraph-block').length){
				jQuery('#respond').appendTo('#paragraph-block-'+(selected_paragraphnumber) + ' .toplevel-respond');			
			}
			else{	
				jQuery('#respond').appendTo('#toplevel-commentbox');
			}
			jQuery(this).removeClass('cancel-response');


		}
	
	});

	
});



jQuery.fn.extend({	

	position_main_elements: function() {
		
		if(typeof override_comment_box != 'undefined'){
			jQuery("#commentbox").css(override_comment_box_specs);
			return;
						
		}
	
		var browser_width = jQuery(window).width();
		var browser_height = jQuery(window).height();
		var content_height = jQuery('#content').height();
		var default_top = parseInt(jQuery('#content').position().top);
		var scroll_top =  default_top  + parseInt(jQuery(window).scrollTop());
		var lock_position = jQuery('#content').offset().top;
		
		
		//console.log(scroll_top + ' - '+ lock_position+"\n");
		//iOS

		if(iOS){
			var ipad_scroll_top_position = (scroll_top < 360) ? 260: (scroll_top - 100) ;

			jQuery("#commentbox-header").css('position',  'absolute');			
			jQuery("#commentbox-header").css('top', ipad_scroll_top_position);			

			jQuery("#commentbox").css('position',  'absolute');			
			jQuery("#commentbox").css('top', ipad_scroll_top_position);
			
			jQuery("#dynamic-sidebar").css('position',  'absolute');			
			jQuery("#dynamic-sidebar").css('top',  ipad_scroll_top_position);				
			
			return;
		}
	
		
			
		//top of page
		if(scroll_top > lock_position && jQuery("#commentbox").css('position') != 'fixed' ){
			var left = parseInt(jQuery('#content').offset().left) + 565  ;			
			jQuery("#commentbox, #commentbox-header").css('position', 'fixed');
			jQuery("#commentbox, #commentbox-header").css('left', left + 'px');
			jQuery("#commentbox-header").css('top', '5px');
			jQuery("#commentbox").css('top',  parseInt(jQuery('#wpadminbar').outerHeight()) +  parseInt(jQuery('#commentbox-header').outerHeight()) + 5 + 'px');
			jQuery("#commentbox").css('height', '90%');
			
		}	
		else if(scroll_top < lock_position && jQuery("#commentbox").css('position') != 'absolute' ){
			jQuery("#commentbox, #commentbox-header").css('position', 'absolute');
			jQuery("#commentbox, #commentbox-header").css('left', '565px'	);
			jQuery("#commentbox-header").css('top', '0px' );
			jQuery("#commentbox").css('top', parseInt(jQuery('#commentbox-header').top) + parseInt(jQuery('#commentbox-header').outerHeight()) + 'px');
			jQuery("#commentbox").css('height', jQuery(window).height() - 250 + 'px');
		}

	
		//bottom of page
		if(scroll_top > (content_height - ((browser_height/2)+20)) && jQuery("#commentbox").css('position') == 'fixed'){
			jQuery("#commentbox").css('height', '50%');
			jQuery("#commentbox").addClass('resized');
		}
		else if(scroll_top < (content_height - ((browser_height/2)+20)) && jQuery("#commentbox").hasClass('resized')){
			jQuery("#commentbox").css('height', '90%');			
			jQuery("#commentbox").removeClass('resized');
		}

		

	}	
	
});


jQuery.fn.openlightbox = function (lightbox, params){
	
	if(isNaN(lightbox)){
		jQuery.post( siteurl + "/ajax/" + lightbox +'/', params, 
			function( data ) {	
		
				if(data && parseInt(data.status) == 1){
					jQuery('#lightbox-content').hide();
					var browser_width = jQuery(window).width();
					var browser_height = jQuery(window).height();
					var body_width = jQuery('#wrapper').width();
					var body_height = jQuery('#wrapper').height();
		
		
					jQuery('.lightbox-submit').removeClass('disabled');
		
					jQuery('#lightbox-transparency').css('width', body_width  + 'px');
					jQuery('#lightbox-transparency').css('height', ( body_height + 70 )+ 'px');
					jQuery('#lightbox-transparency').fadeTo(0, 0.70);				
					jQuery('#lightbox-content').html(data.message);

					jQuery('#lightbox-content').css('left', (browser_width - jQuery('#lightbox-content').width()) /2  );
					jQuery('#lightbox-content').css('top', '20%');
					jQuery('#lightbox-content').fadeIn('slow');


					function_name = lightbox.replace(/-/g, '_');// + "_ajax_result";

					var dynamic_call = 'typeof(AjaxResult.' + function_name + ') != "undefined"';
					
					if(eval(dynamic_call)){
						eval('AjaxResult.' + function_name + '(data);');
					}
					else{
					}

					var focus = setTimeout(function() {
						jQuery('#lightbox-content input:first').focus();				    
						}, 1000);

					
					if(jQuery('#lightbox-content .lightbox-delay-close').length){
						var t = setTimeout(function() {
							jQuery("body").closelightbox();
							}, 1000);
						jQuery(this).data('timeout', t);			
					}
				}
		
			}, 'json' );
	}
}




jQuery.fn.closelightbox = function (){
	jQuery('#lightbox-content').fadeOut();
	jQuery('#lightbox-transparency').css('width', 0);
	jQuery('#lightbox-transparency').css('height', 0);
	document.location.hash.length = '';
}

jQuery.fn.displayerrorslightbox = function (data){
	if(data.status == 0){
		var lightbox = '#lightbox-generic-response';
		jQuery(lightbox + ' > p').html(data.message);
		jQuery('body').openlightbox(lightbox);	
	}
}

jQuery.fn.load_in_lightbox = function (data){
	if(parseInt(data.status) == 1){
		jQuery('#lightbox-content').hide();
		var browser_width = jQuery(window).width();
		var browser_height = jQuery(window).height();
		var body_width = jQuery('#wrapper').width();
		var body_height = jQuery('#wrapper').height();


		jQuery('.lightbox-submit').removeClass('disabled');

		jQuery('#lightbox-transparency').css('width', body_width  + 'px');
		jQuery('#lightbox-transparency').css('height', ( body_height + 70 )+ 'px');
		jQuery('#lightbox-transparency').fadeTo(0, 0.70);				
		if(data.message.content){
			jQuery('#lightbox-content').html(data.message.content);						
		}
		else{
			jQuery('#lightbox-content').html(data.message);			
		}

		jQuery('#lightbox-content').css('left', (browser_width - jQuery('#lightbox-content').width()) /2  );
		jQuery('#lightbox-content').css('top', '20%');
		jQuery('#lightbox-content').fadeIn('slow');
	}
}







function commentbox_closed_state(){
	//jQuery('#respond').appendTo('#comments-toolbar');
	jQuery('#comment_parent').val(0);
	jQuery('#comment').val('Discuss Here...');
	jQuery('#commentbox').css('overflow-y', 'scroll');
	//jQuery('#submit-comment').css('display', 'none');

	jQuery('#comments-toolbar').show();
}

function commentbox_reply_state(){
	
	var selected_comment_id = jQuery.cookie('selected_comment_id');				
	var selected_blog_id = jQuery.cookie('selected_blog_id');					
	var reply_box = '#comment-'+ selected_blog_id + '-' +selected_comment_id + ' .comment-respond';

	jQuery('#respond').appendTo(reply_box);		
	jQuery('#respond').fadeIn();
	jQuery('.reply_box').show();
	jQuery('#submit-comment').show();	
}


function commentbox_open_state(){
	//jQuery('#respond').appendTo('#comments-toolbar');
	jQuery('#comment_parent').val(0);
	jQuery('#comment').val('Discuss Here...');
	jQuery('#commentbox').css('overflow-y', 'scroll');
	jQuery('#comment').removeClass('comment-expanded');
	jQuery('#comment').addClass('comment-collapsed');
}

function commentbox_expanded_state(){
	//jQuery('#respond').appendTo('#comments-toolbar');
	jQuery('#comment_parent').val(0);
	jQuery('#comment').val('');
	jQuery('#commentbox').css('overflow-y', 'scroll');
}



