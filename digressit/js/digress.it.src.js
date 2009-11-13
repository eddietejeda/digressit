/*
Plugin Name: Digress.it
Plugin URI: http://digress.it
Description:  Digress.it allows readers to comment paragraph by paragraph in the margins of a text. You can use it to annotate, gloss, workshop, debate and more!
Author: Eddie A Tejeda
Version: 2.2
Author URI: http://www.visudo.com
License: GPLv2 (http://creativecommons.org/licenses/GPL/2.0/)



The functions: containerResize, buildContainers, setIcon , setButtons, minimize and iconize where developed by Matteo Bicocchi on JQuery
© 2002-2008 Open Lab srl, Matteo Bicocchi
www.open-lab.com - info@open-lab.com
version 1.0
GPL (GPL-LICENSE.txt) licenses.
*/

var msie=jQuery.browser.msie;
var msie6=jQuery.browser.msie && jQuery.browser.version=="6.0";
var msie7=jQuery.browser.msie && jQuery.browser.version=="7.0";
var msie8=jQuery.browser.msie && jQuery.browser.version=="8.0";
var safari=jQuery.browser.safari;
var mozilla=jQuery.browser.mozilla;
var zi=10000;
var on_load_selected_paragraph;
var window_has_focus = true;


//


if(!msie){
	(function(jQuery) {
	    jQuery.fn.poll = function(options){
			
			var $this = jQuery(this);
			// extend our default options with those provided
			var opts = jQuery.extend({}, jQuery.fn.poll.defaults, options);
			setInterval(update, opts.interval);

			// method used to update element html
			function update(){
				
				if(window_has_focus){
				
					jQuery.ajax({
						type: opts.type,
						url: opts.url,
						success: opts.success
					});
				}
			};
		};

		// default options
		jQuery.fn.poll.defaults = {
			type: "POST",
			url: ".",
			success: '',
			interval: 10000
		};
	})(jQuery);
}


if(total_comment_count > jQuery.cookie('total_comment_count'))
{

	jQuery.getJSON(wp_path + '?digressit-event=approved_comments&current-count=' + jQuery.cookie('total_comment_count'),
		function(new_comments){
			
		});

}


jQuery(document).ready(function(){


	//recreate comment section
	jQuery("body").append('<div id="accordion"></div>');
	jQuery("#respond").css('display', 'block');
	jQuery(".commentlist").css('display', 'block');
	
	

	
	jQuery.cookie('total_comment_count', total_comment_count , { path: '/', expires: 1} );	
	jQuery.cookie('comment_count', comment_count , { path: '/', expires: 1} );
	jQuery.cookie('text_selection', null , { path: '/', expires: 1} );

	

	if(default_skin == 'none'){
		jQuery.cookie('top_position_commentbox', null,  { path: '/', expires: 1} );	
		jQuery.cookie('left_position_commentbox', null,  { path: '/', expires: 1} );	
	}
	
	/************************************************/
	//
	//               Ajax Polling
	//  
	/************************************************/
	

	jQuery(window).blur(function(){
		window_has_focus =false;
	}).focus(function(){
		window_has_focus =true;
	});
	
	if(!msie){
	
	jQuery("#accordion").poll({
	    url: wp_path + '?digressit-event=comment_count',
	    interval: 20000,
	    type: "GET",
	    success: function(data){
			
			//alert(data);
			var new_count = data;
			
			
			if( jQuery.cookie('total_comment_count') == 'null' ){
				jQuery.cookie('total_comment_count', total_comment_count);
			}
			
			var current_comment_count = jQuery.cookie('total_comment_count');
			
			if(new_count > current_comment_count){
				jQuery.getJSON(wp_path + '?digressit-event=approved_comments&current-count=' + current_comment_count,
					function(new_comments){
						
						//alert(new_comments);
						jQuery.cookie('total_comment_count', new_count , { path: '/', expires: 1} );
						
						//var new_comments = eval("(" + data + ")");
						
						//alert(new_comments.length);

						for(var i = 0; i < new_comments.length; i++){
							
							var new_comment = new_comments[i];
							
							if(new_comment['comment_post_ID'] == post_ID){
								var new_comment_text;
								new_comment_text= '<div id="comment-' + new_comment['comment_ID'] + '" class="comment byuser comment-author-admin bypostauthor odd alt thread-even depth-1 parent"> ' +
									'<div class="comment-body" id="div-comment-' + new_comment['comment_ID'] + '"> ' +
									'<div class="comment-author vcard">' +
									'<cite class="fn">' + new_comment['comment_author'] + '</cite> <span class="says">says:</span>		</div>' +
									'<div class="comment-meta commentmetadata"><a href="'+ window.location.href +'#comment-'+ new_comment['comment_ID'] +'">'+ new_comment['comment_date'] +'</a> </div>' +
									'<p>' + new_comment['comment_content'] + '</p>' +
									'<div class="reply">' +
									'<a onclick=\'return addComment.moveForm("div-comment-'+ new_comment['comment_ID'] +'", "'+ new_comment['comment_ID'] +'", "respond", "'+ new_comment['comment_parent'] +'")\' href="'+ window.location.href +'?replytocom='+ new_comment['comment_ID'] +'#respond" class="comment-reply-link" rel="nofollow">Reply</a></div>' +
									'</div>' +
								'</div>';
							
								if(new_comment['comment_parent'] > 0){
									jQuery('#comment-' + new_comment['comment_parent']).append(new_comment_text);
								}
								else{
									jQuery('#comment-group-' + new_comment['comment_text_signature']).append(new_comment_text);
								}
							
								var add_count_path = '#comment-block-' + new_comment['comment_text_signature'] + ' .commentarea_commentcount';
								var add_count = parseInt(jQuery(add_count_path).text()) + 1;
								jQuery(add_count_path).text(add_count);
							
							

								jQuery('.commentcount').eq(new_comment['comment_text_signature'] - 1).text(add_count);
								
								
								var selected_block = '#comment-block-' + new_comment['comment_text_signature'];

								jQuery(selected_block + ' .commentarea').pulse({
								    speed: 800,
	    							backgroundColors: ['#AAA', '#FFF'],
								    opacityRange: [0.3,0.8]
								});
							
							/*
								jQuery('#comment-' + new_comment['comment_ID'] ).pulse({
								    speed: 1000,
	    							backgroundColors: ['#EEE', '#FFF'],
								    opacityRange: [0.3,0.8]
								});
							*/	
							}
							else{
								jQuery('#new-comment-message').fadeIn();
							}
							
							
							
						}
					}
				);
			}
	    }
	});
	}



	
	/************************************************/
	//
	//               DOM Manipulation
	//  
	/************************************************/


	
	
	//the wholepage group... if the paragraph signature is empty, group it as entire page commnet
	var whole_page_count = 0;
	var comment_ids = new Array();	
	for ( var j in commment_text_signature )
	{
		if(commment_text_signature[j] == '' || commment_text_signature[j] == '0')
		{
			comment_ids[whole_page_count] = '#comment-' + j;			
			whole_page_count++;
		}		
	}	
	
	var plural = (whole_page_count == 1) ? '' : 's';
	jQuery("#accordion").append('<div class="comment-block-class" id="comment-block-0"><div class="commentarea"><span class="read_num_comment_general" id="comment-0" ><span class="commentarea_commentcount">'+whole_page_count+'</span> general comment'+plural+'</span> <span class="write-comment-action"></span> </div><div class="commentblock"><span class="paragraph_feed"></span><div class="subcommentlist" id="comment-group-0"></div></div></div>');
	for( var k in comment_ids)
	{
		jQuery('#comment-group-0').append( jQuery(comment_ids[k]) );			
	}


	//this is not the most efficient way to do this, but it works alright. we go through each paragraph and see which
	//of the comment signatures, found in commment_text_signature, matches and we put that comment in the right place.
	jQuery('.commenticon').each( function (i) {
		
		var comments = new Array();
		var comment_ids = new Array();
		var count = 0;
		var text_signature = '';
		for ( var j in commment_text_signature )
		{
			if( similarString( commment_text_signature[j], (jQuery('.commenticon').index(this)+1) )  )
			{
				
				comments[count] = commment_text_signature[j];
				
				var commentname = '#comment-' + j;
				var classes;
				if(jQuery(commentname).length > 0){

					classes = jQuery(commentname).attr('class').split(' ');
					if( classes ){
						for(var z in classes){
							if(classes[z] == 'depth-1'){
								comment_ids[count] = commentname;							
							}
						}
					}
					count++;
				}
			}
			text_signature = commment_text_signature[j];
		}
		
		var paragraph = i + 1;
		var plural = (count == 1) ? '' : 's';
		jQuery("#accordion").append('<div  class="comment-block-class" id="comment-block-' + paragraph + '"><div class="commentarea"><span class="paragraph_number_large">'+ paragraph +'</span> <span class="read_num_comment" id="comment-'+ paragraph + '">  <span class="commentarea_commentcount">'+count+'</span> comment'+plural+'  </span><span class="write-comment-action"></span></div><div class="commentblock"><span class="paragraph_feed"></span><div class="subcommentlist" id="comment-group-'+ paragraph +'"></div></div></div>');		
		
		for( var k in comment_ids)
		{
			jQuery('#comment-group-'+ paragraph).append( jQuery(comment_ids[k]) );			
		}		
	});
	jQuery('#comment-group-0').append( jQuery('#respond') );

	
	//if there is no comment area, things break. lets just create one to be safe
	var commentstag = '';
	if(  jQuery('#comments').length  > 0) 
	{
		commentstag = '#comments';
	}/*
	else if(  jQuery('#idc-container-parent').length  > 0) {
		commentstag = '#idc-container-parent';
	}*/
	else{
		jQuery('body').append("<div id='comments'></div>");
		commentstag = '#comments';		
	}




	
	if( jQuery('.nocomments').length > 0 ){		

		jQuery('#accordion').append( jQuery('.nocomments') );			
		jQuery('.nocomments').css('padding', '20px 10px 0px 10px');
		
 		//FIXME: don't just hide. remove from the algo
		jQuery('#comment-block-0').css( 'display', 'none');			
		jQuery('#comment-block-1').css( 'display', 'none');			
		
		
	}


	var user_buttons = resizable = draggable = minimized = iconized = '';
	//if(allow_users_to_iconize){ user_buttons = 'i,'; } //i
	if(allow_users_to_minimize && !msie6){ /* user_buttons += 'm'; */	}
	if(allow_users_to_resize && false){ resizable = 'resizable'; }
	if(allow_users_to_drag && !msie6){ draggable = 'draggable'; }

	if(jQuery.cookie('minimized') == true){ minimized = "minimized = 'true' "; }
	//if(jQuery.cookie('iconized')){ iconized = "iconized = 'true' "; }


	//get the existing comment area, and put put it around new html, which will be the floating box
	jQuery(commentstag + ', .commentlist').wrapAll('<div id="commentbox" ' + minimized + ' class="container '+ resizable + ' ' + draggable + ' ' + default_skin+'"  buttons="'+user_buttons+'"><div id="commentwindow"></div></div>');
	jQuery('#commentbox').appendTo('body');
	jQuery('#commentbox').html('<table cellpadding="0" cellspacing="0" class="containerTable"><tr class="top"><td class="no"></td><td class="n"><h3 id="commentoverview">Comments Overview</h3><div id="new_comment">new comment</div></td><td class="ne">&nbsp;</td></tr><tr unselectable="on" class="middle"><td class="o"> </td><td class="c" id="comment_contents"></td><td class="e"> </td></tr><tr class="bottom"><td class="so"> </td><td class="s"></td><td class="se"> </td></tr></table>');
	jQuery('#commentboxtitle').html( jQuery(commentstag).html() );	
	jQuery("#accordion").appendTo("#comment_contents");	
		
	//build the comment area
	jQuery(".container").buildContainers();
	



	

	//position the commenbox
	var commentbox = jQuery("#commentbox");
	var left = commentbox.position().left;
	var top = commentbox.position().top;
	var width = commentbox.width();      
	var height = commentbox.height();
	

	if(msie6 || msie7){
		jQuery("#commentbox").css('height' , '200px');
		jQuery("#commentbox").css('width', '420px');
	}
	if(msie7){
		jQuery(".containerTable").css('width', '420px');
	}


    jQuery(window).scroll(function () { 
		jQuery('#commentbox').css({position:"fixed"});

		if(safari || msie6){

			//alert(jQuery.cookie('top_position_commentbox'));
			//alert(jQuery.cookie('left_position_commentbox'));
			var default_top = parseInt(jQuery.cookie('top_position_commentbox'));
			var default_left = parseInt(jQuery.cookie('left_position_commentbox'));

			var top =  default_top  + parseInt(jQuery(window).scrollTop());
			var left = default_left +  parseInt(jQuery(window).scrollLeft());


			jQuery('#commentbox').css({position:"absolute"});
			jQuery("#commentbox").css('top',  top+'px');
			jQuery("#commentbox").css('left', left+'px');



		}
    });



	



	function getParagraphNumberByCommentId(comment_id)
	{
		var text_signature = getTextSignatureByCommentId(comment_id);
		for ( var i in text_signatures )
		{
			if( similarString( text_signatures[i], text_signature) )
			{
				return i+1;
			}
		}
	}
	
	/*
	

	jQuery(window).keypress(function(e){		

		var target = e.target;
		var $kids = jQuery(target).children();
		var keycodes=new Array();
		keycodes[48] = '0';
		keycodes[49] = '1';
		keycodes[50] = '2';
		keycodes[51] = '3';
		keycodes[52] = '4';
		keycodes[53] = '5';
		keycodes[54] = '6';
		keycodes[55] = '7';
		keycodes[56] = '8';
		keycodes[57] = '9';
	


	
		if(parseInt(e.charCode) > 47 && parseInt(e.charCode) < 58){
		
			var paragraph_code = text_signatures[ keycodes[e.charCode]-1 ];
			selectParagraph(paragraph_code);
			return document.defaultAction = false;				
		}
	
	});
	*/	
	
	function getTextSignatureByParagraphNumber(paragraph){
		return paragraph;//text_signatures[paragraph];
	}

	function getTextSignatureByCommentId(comment_id)
	{
		return commment_text_signature[comment_id];
	}

	function getCommentIdByTextSignature(signature)
	{
		return jQuery.inArray(signature, commment_text_signature);
	}

	function getParagraphNumberByTextSignature(signature)
	{
		return jQuery.inArray(signature, text_signatures);
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

	/************************************************/
	//
	//               Major Interaction Code 
	//  
	/************************************************/
			
	jQuery(window).mousedown(function(event)  {
		var target = event.target;		
		var $kids = jQuery('.containerTable').children();
		
		//alert(  jQuery(target).attr('id')  );
		if(  !$kids.find( jQuery(target).attr('id') ) ||  jQuery(target).attr('id') == 'page' ){
			jQuery('.textblock').unhighlightText();
			cancelComment();
		}
	});
	
	
	function cancelComment(){
		jQuery('input#comment_parent').val('0');
		jQuery('#cancel-comment-reply-link').css('display' , 'none');
		jQuery('#comment-group-').append( jQuery('#respond') );
		document.location.hash  = "0";

	}


	
	/*
	jQuery('.paragraphtext').mouseup(function(event){

		var text_selection = getSelection()
		if( text_selection ){
			jQuery.cookie('text_selection', text_selection, {path: '/'});
			highlightSelection();
		}

		var body = document.getElementsByTagName("body")[0];
		window.getSelection().collapse(body,0);			
		
	});*/
	
/*		
	jQuery.fn.highlightToggle = function(speed, easing, callback) {
		var width = this.width() + parseInt(this.css('paddingLeft')) + parseInt(this.css('paddingRight'));
		this.animate({marginRight: parseInt(this.css('marginRight')) <0 ? 0 : -width}, speed, easing, callback);
		jQuery.cookie('sidebar', this.css('marginRight'), { path: '/'} );			
		
	};


	jQuery('#togglerbutton').click(function() {
		jQuery('#sidebar').blindToggle('slow');
	});
*/
		
	/** 
	 * @description: clicking on the little comment icon
	 * @todo: 
	 *
	 */
	jQuery(".commenticon").click(function (e) {
		
		
		var paragraphnumber = jQuery('.commenticon').index(this) + 1; //getTextSignatureByParagraphNumber();

		if(jQuery.cookie('text_signature') == paragraphnumber){
			jQuery('.textblock').unhighlightText();
		}
		else{		
			jQuery('.textblock').selectParagraph(paragraphnumber);
			document.location.hash = "#" + paragraphnumber;		
			jQuery('.containerBody').scrollTo( jQuery('#comment-block-' + paragraphnumber), 200);			
		}
	});
	
	

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
	
/*	
    jQuery(".comment").hover(
      function () {
		var selection = '#selection-' + jQuery(this).attr('id').substring(8);
		jQuery(selection).css('text-decoration', 'underline');
      }, 
      function () {
		var selection = '#selection-' + jQuery(this).attr('id').substring(8);
		jQuery(selection).css('text-decoration', 'none');
		jQuery(this).recover();
      }
    );
*/

	jQuery(".comment").hover(function(){
		jQuery(this).recover();
	});

    jQuery(".paragraphnumber").hover(
		function () {
			var perma =jQuery('.paragraphnumber').index(this);
			var embed  = jQuery(jQuery('.embedcode').get(perma)).fadeIn(200);
		}, 
		function () {
			var perma =jQuery('.paragraphnumber').index(this);
			var embed  = jQuery(jQuery('.embedcode').get(perma)).fadeOut(200);
		}
    );
	
	

	
	
		
	/** 
	 * @description: clicking on the commentarea paragraph block
	 * @todo: 
	 *
	 */
	jQuery(".commentarea").click(function (e) {

		var paragraphnumber = jQuery(this).parent().attr('id').substring(14);

		//alert(paragraphnumber);
 		if( paragraphnumber == jQuery.cookie('text_signature'))
		{
			jQuery('.textblock').unhighlightText();
		}		
		else
		{
			
			//alert(paragraphnumber);
			
			jQuery('.textblock').highlightParagraph(paragraphnumber);
			jQuery('#comment-group-'+ paragraphnumber).append( jQuery('#respond'));
			jQuery.cookie('text_signature', paragraphnumber, { path: '/', expires: 1} );
			
			
			
			if(jQuery(e.target).attr('class') == 'write-comment-action'){
				jQuery('#comment_contents > .containerBody').scrollTo( jQuery('#respond'), 1000);								
			}
			else{
				jQuery('#comment_contents > .containerBody').scrollTo( jQuery('#comment-block-' + (paragraphnumber)), 1000);				
			}
			document.location.hash = "#" +(paragraphnumber);
			jQuery(this).recover();
			
		}
	});



	//href="http://annotate.it/?register=<?php echo bloginfo('wpurl'); ?>"
	
	jQuery("#digressit-register").click(function (e){		
		//alert(jQuery(this).attr('name'));
	});
	


	/************************************************/
	//
	//               Minor Interaction Code 
	//  
	/************************************************/

	/*
	jQuery("#accordion").accordion({
		header: "h6",
		alwaysOpen: false,
		animated: false,
		collapsible: true

		
	});

	jQuery('#accordion').accordion('activate', false);
	*/


	//jQuery("#respond h3").empty();
	jQuery("textarea").attr('rows', 5);
	
	
	if(allow_users_to_resize && (!msie6 || !msie7 ) ){
		jQuery(".containerTable").resizable({
			transparent: true,
			handles: "s,e,se",
			stop: function (e) { resizing(e); }
		});	
	}
		
	//we need to do this to have fixed window. or else things get weird!
	function resizing(e){
		var height = parseInt(jQuery('.containerTable').css('height'));
		var width = parseInt(jQuery('.containerTable').css('width'));

		// make sure we dont go less than min height
		if(height < 365){
			height = 365;
		}
		


		jQuery('.containerBody').css('height', (height) + 'px');
		jQuery('.containerBody').css('width', width + 'px');


		jQuery('#commentbox').css('height', height + 'px');
		jQuery('#commentbox').css('width', width + 'px');



		jQuery('.ui-resizable-e').css('height', height + 'px');
		jQuery('.ui-resizable-s').css('width', width + 'px');


		//jQuery.cookie('commentbox_height', height + 'px',  { path: '/', expires: 1} );	
		//jQuery.cookie('commentbox_width', width + 'px',  { path: '/', expires: 1} );	


	}
	

	jQuery('#commentwindow').mousewheel(function(event, delta) {
		return false; // prevent default
	});


	jQuery('input, textarea').click(function (e) {
		jQuery(this).focus();
	});
		



	
    // http://kevin.vanzonneveld.net
	function levenshtein (a, b)
	{
	    var min=Math.min, len1=0, len2=0, I=0, i=0, d=[], c='', j=0, J=0;

	    // BEGIN STATIC
	    var split = false;
	    try
		{
	        split=!('0')[0];
	    } catch(i){
	        split=true; // Earlier IE may not support access by string index
	    }
	    // END STATIC

	    if (a == b) {
	        return 0;
	    }
	    if (!a.length || !b.length) {
	        return b.length || a.length;
	    }
	    if (split){
	        a = a.split('');b = b.split('');
	    }
	    len1 = a.length + 1;
	    len2 = b.length + 1;
	    d = [[0]];
	    while (++i < len2) {
	        d[0][i] = i;
	    }
	    i = 0;
	    while (++i < len1) {
	        J = j = 0;
	        c = a[I];
	        d[i] = [i];
	        while (++j < len2) {
	            d[i][j] = min(d[I][j] + 1, d[i][J] + 1, d[I][J] + (c != b[J]));
	            ++J;
	        }
	        ++I;
	    }

	    return d[len1 - 1][len2 - 1];
	}	


	function getElementXPath(elt)
	{
	     var path = "";
	     for (; elt && elt.nodeType == 1; elt = elt.parentNode)
	     {
	   	idx = getElementIdx(elt);
		xname = elt.tagName;
		if (idx > 1) xname += "[" + idx + "]";
		path = "/" + xname + path;
	     }

	     return path;	
	}

	function getElementIdx(elt)
	{
	    var count = 1;
	    for (var sib = elt.previousSibling; sib ; sib = sib.previousSibling)
	    {
	        if(sib.nodeType == 1 && sib.tagName == elt.tagName)	count++
	    }

	    return count;
	}
	jQuery('.textblock').unhighlightText();

	
	//if there is an anchor, scroll to it, nicely.
	if (document.location.hash.length) {
		
		jQuery('html, body').scrollTo(0, 0);
		jQuery('.container .containerBody').scrollTo( 0, 0);

		
		var anchor = document.location.hash.substring(1);
		var search = document.location.search;
		var signature = false;
		var commentId = false;
		if( !isNaN(anchor))
		{
			signature = getTextSignatureByParagraphNumber(anchor);
		}		
		else if( anchor.substring(0, 8) == 'comment-')
		{
			commentId = anchor.substring(8);
			signature = getTextSignatureByCommentId( commentId  );			
			
			
		}		
		else if( search.length && getQueryVariable('replytocom'))
		{
			signature = getTextSignatureByCommentId(  getQueryVariable('replytocom') );
		}
		
		if(signature !== false)
		{
			jQuery('.textblock').selectParagraph(signature);	
			jQuery('#comment-group-'+ signature).append( jQuery('#respond'));
			
			if(commentId !== false)
			{
				jQuery('.container .containerBody').scrollTo( jQuery('#comment-' + commentId), 200);
			}
			
			var paragraphnumber = getCommentIdByTextSignature(signature);
			on_load_selected_paragraph = paragraphnumber;			
		}
	}

	if(default_skin == 'none'){

		if(default_skin == 'none'){
			default_left_position = parseInt(jQuery('#page').css('padding-left')) +  parseInt(jQuery('#content').css('width')) + parseInt(jQuery('#content').css('padding-left')) + parseInt(jQuery('#content').css('padding-right')) + parseInt(jQuery('#content').css('margin-left')) + parseInt(jQuery('#content').css('margin-right'))  -  parseInt(10);
		}
		
		jQuery('#commentbox').css({position:"fixed"});
		//alert('left4: '+ jQuery("#commentbox").css("left"));
		
	}
	else if(jQuery.cookie('left_position_commentbox') && jQuery.cookie('top_position_commentbox'))
	{

		var left = parseInt(jQuery.cookie('left_position_commentbox'));
		var top = parseInt(jQuery.cookie('top_position_commentbox'));
		
		jQuery("#commentbox").css("left", left + 'px');
		jQuery("#commentbox").css("top", top + 'px');


	}
	else if(msie6)
	{
		jQuery("#commentbox").css("top", '75px');
		jQuery("#commentbox").css("left", '50%');
	}		
	else
	{	
		var browser_width =  parseInt(jQuery(window).width()) ;
		var browser_height =  parseInt(jQuery(window).height()) ;




		
		if(default_skin == 'none'){
			default_left_position = parseInt(jQuery('#page').css('padding-left')) +  parseInt(jQuery('#content').css('width')) + parseInt(jQuery('#content').css('padding-left')) + parseInt(jQuery('#content').css('padding-right')) + parseInt(jQuery('#content').css('margin-left')) + parseInt(jQuery('#content').css('margin-right')) -  parseInt(10);
		}
		
		if( default_left_position.indexOf('%')){
			default_left_position = browser_width *  parseFloat('0.' + parseInt(default_left_position));
		}
		/*
		if( default_top_position.indexOf('%')){
			default_top_position = browser_height * parseFloat('0.' +  parseInt(default_top_position));
		}
		*/
		
		jQuery.cookie('top_position_commentbox', parseInt(default_top_position),  { path: '/', expires: 1} );	
		jQuery.cookie('left_position_commentbox',parseInt(default_left_position),  { path: '/', expires: 1} );	
		
		
		jQuery("#commentbox").css("top", parseInt(default_top_position) + 'px');
		jQuery("#commentbox").css("left", parseInt(default_left_position)+ 'px');
		

		
	}
	
	
	
	if(jQuery.cookie('commentbox_height') && jQuery.cookie('commentbox_width') && !msie7)
	{		
		jQuery("#commentbox").css("height", jQuery.cookie('commentbox_height'));
		jQuery("#commentbox").css("width", jQuery.cookie('commentbox_width'));

	}
	
	

	


});




jQuery.fn.unhighlightText = function (speed){
	return this.each (function ()
	{
		var container=jQuery(this);		
		//speed = typeof(speed) != 'undefined' ? speed : '';		
		var value = jQuery.cookie('text_signature');		
		jQuery('#accordion .commentblock').hide();
		//jQuery('#selected_block').fadeOut('fast', function(e){ jQuery('#selected_block').remove() } );		
		jQuery.cookie('text_signature', null, { path: '/', expires: 1} );
		jQuery(container).removeClass('selected_block');	
	});
	
}

jQuery.fn.unhighlightSelection = function (){
	
	return this.each (function ()
	{
		var container=jQuery(this);
		
		if(allow_text_selection){	
			jQuery.cookie('text_selection', 0, { path: '/', expires: 1} );
			jQuery('#selected_text').replaceWith( jQuery('#selected_text').text() );		
			jQuery('#selected_text').remove();	
		}	
		else{
			return false;
		}
	});
	
}



jQuery.fn.getSelection = function (){
	
	return this.each (function ()
	{
		var container=jQuery(this);
		
		if (window.getSelection && allow_text_selection) {
			return window.getSelection();
		}
		else{
			return false;
		}
	});	
}

jQuery.fn.getRangeObject = function (selectionObject) {

	return this.each (function ()
	{
		var container=jQuery(this);
		
		if (selectionObject.getRangeAt && allow_text_selection){
			ranges = [];
			for(var i = 0; i < selectionObject.rangeCount; i++) {
				if( i > 0){
					selectionObject.getRangeAt(i).collapse();
				}
			}

			return selectionObject.getRangeAt(0);
		}
		else{
			return false;
		}
		
	});
}



var similarString = function (str1, str2){		


	if( str1 == str2){
		return true;
	}
	return false;
}


jQuery.fn.selectParagraph = function (paragraphnumber){
	var container=jQuery(this);
	
	jQuery(container).highlightParagraph(paragraphnumber);
	jQuery('#comment-group-'+ paragraphnumber).append( jQuery('#respond'));
	jQuery.cookie('text_signature', paragraphnumber, { path: '/', expires: 1} );				
	//jQuery('#accordion').accordion('activate',  parseInt(text_signature) );
}



jQuery.fn.highlightParagraph = function (paragraphnumber){
	//return this.each (function ()
	//{
		var container=jQuery(this);

		jQuery(container).unhighlightText();

		jQuery('#comment-block-' + paragraphnumber + ' > div').show();

		if(paragraphnumber == 0){
			return;
		}

		//var paragraphnumber = getParagraphNumberByTextSignature(text_signature);
	
		var textblockname = "#textblock-" + paragraphnumber;
		var textblock = jQuery(textblockname);
		var commentbox = jQuery("#commentbox");

		var left = textblock.position().left;
		var top = textblock.position().top;


		var width = textblock.width() + parseInt(jQuery(textblockname).css('padding-left').substr(0, (jQuery(textblockname).css('padding-left').length - 2) )) + parseInt(jQuery(textblockname).css('padding-right').substr(0, (jQuery(textblockname).css('padding-right').length - 2) ));
		var height = textblock.height() + parseInt(jQuery(textblockname).css('padding-top').substr(0, (jQuery(textblockname).css('padding-top').length - 2) )) + parseInt(jQuery(textblockname).css('padding-right').substr(0, (jQuery(textblockname).css('padding-right').length - 2) ));


		if(safari){
			//top = top + 135;
		}

		var scrollto = (top > 200)  ? (top - 100) : 0;
	
		jQuery('html, body').scrollTo(scrollto, 200);
		//alert(paragraphnumber);
		jQuery('.containerBody').scrollTo( jQuery('#comment-block-' + paragraphnumber), 200);
	
		jQuery(textblock).highlightBlock(paragraphnumber) ;

	//});
}


jQuery.fn.highlightBlock = function(paragraphnumber) {

	return this.each (function ()
	{
		var container=jQuery(this);		
		jQuery(container).addClass('selected_block');
	});

}







jQuery.fn.savePosition = function (opt){
	return this.each (function ()
	{
		
		var container=jQuery(this);
		var browser_width =  parseInt(jQuery(window).width());
		var browser_height =  parseInt(jQuery(window).height());
		var browser_scroll_top = parseInt(jQuery(window).scrollTop());
		var commentbox_height = parseInt(jQuery(this).height());
		var commentbox_top = parseInt(jQuery(container).position().top);
		var commentbox_left = parseInt(jQuery(container).position().left);
		var commentbox_relative_top = (commentbox_top - browser_scroll_top);

		commentbox_left = (commentbox_left > 0) ? commentbox_left : 1;
		commentbox_relative_top = (commentbox_relative_top > 0) ? commentbox_relative_top : 1;



		jQuery.cookie('left_position_commentbox', commentbox_left , { path: '/', expires: 1} );
		jQuery.cookie('top_position_commentbox', commentbox_relative_top , { path: '/', expires: 1});
	});
}


//function savePosition(){
//}


jQuery.fn.buildContainers = function (){
	return this.each (function ()
	{
		this.options = {
			containment:image_path + '/commentbox/',
			elementsPath:image_path + '/commentbox/'
		}

		var container=jQuery(this);
		container.find(".containerTable:first").css("width","100%");
		container.find(".c:first").wrapInner("<div class='containerBody'style='display:block'></div>");
		container.find(".n:first").attr("unselectable","on");
		var icon=container.attr("icon")?container.attr("icon"):"";
		var buttons=container.attr("buttons")?container.attr("buttons"):"";
		container.setIcon(icon, this.options);
		container.setButtons(buttons, this.options);
		if (msie6 && container.css("height")=="auto") container.find(".containerBody:first").hide();
		container.find(".containerBody:first", ".c:first").css("height",container.outerHeight()-container.find(".n:first").outerHeight()-container.find(".s:first").outerHeight());
		if (msie6 && container.css("height")=="auto") container.find(".containerBody:first").show();
		if (container.hasClass("draggable") ){
			
			if(mozilla){
				container.css({position:"fixed"});			
			}
			else{
				container.css({position:"absolute"});
			}
		
			container.find(".n:first").css('cursor', 'move');
			container.css({zIndex:zi++});
			
			if(msie6 || msie7){
				
			}
			else{
				container.draggable({handle:".n:first",cancel:".c",delay:0, containment:"document", stop: function(){ container.savePosition();  } });				
			}
			
			
			container.bind("mousedown",function(){
				jQuery(this).css({zIndex:zi++});
			}
				);
		}
		if (container.hasClass("resizable")){
			container.containerResize(this.options);
		}
		if (container.attr("minimized")=="true"){
			container.attr("minimized","false");
			jQuery.cookie('minimized', false, { path: '/', expires: 1}); 
			
			container.minimize(this.options);
		}
		jQuery(this).attr("minimized","false");

		if (container.attr("iconized")=="true"){
			container.attr("iconized","false");
			container.iconize(this.options);
			jQuery.cookie('minimized', false, { path: '/', expires: 1});
		}
		jQuery(this).attr("iconized","false");
	});
}
jQuery.fn.containerResize = function (){
	jQuery(this).resizable({
		handles:jQuery(this).hasClass("draggable") ? "":"s,e",
		minWidth: 150,
		minHeight: 150,
		helper: "proxy",
		transparent: !jQuery.browser.msie,
		autoHide: !msie6,
		stop:function(e,o){
			var resCont=msie6 ?o.helper:jQuery(this);
			this.elHeight= resCont.outerHeight()-jQuery(this).find(".n:first").outerHeight()-jQuery(this).find(".s:first").outerHeight();
			//jQuery(this).find(".containerBody:first",".c:first").css({height: this.elHeight});
		}
	});
}
jQuery.fn.setIcon = function (icon, opt){
	if (icon !="" ){
		jQuery(this).find(".no:first").append("<img class='icon' src='"+opt.elementsPath+icon+"' style='position:absolute'>");
	}
	else{
		jQuery(this).find(".n:first").css({paddingLeft:"0"});
	}
}

jQuery.fn.setButtons = function (buttons, opt){
	var container=jQuery(this);
	if (buttons !=""){
		var btn=buttons.split(",");
		jQuery(this).find(".ne:first").append("<div class='buttonBar' style='position:absolute'></div>");
		for (var i in btn){
			if (btn[i]=="c"){
				jQuery(this).find(".buttonBar:first").append("<img src='"+opt.elementsPath+"close.png' class='close'>");
				jQuery(this).find(".close:first").bind("click",function(){container.fadeOut(200)});
			}
			if (btn[i]=="m"){
				jQuery(this).find(".n:first").attr("unselectable","on");
				jQuery(this).find(".buttonBar:first").append("<img src='"+opt.elementsPath+"min.png' class='minimizeContainer'>");
				jQuery(this).find(".minimizeContainer:first").bind("click",function(){container.minimize(opt)});
				jQuery(this).find(".n:first").bind("dblclick",function(){container.minimize(opt)});
			}
			if (btn[i]=="p"){
				jQuery(this).find(".buttonBar:first").append("<img src='"+opt.elementsPath+"print.png' class='printContainer'>");
				jQuery(this).find(".printContainer:first").bind("click",function(){});
			}
			if (btn[i]=="i"){
				jQuery(this).find(".buttonBar:first").append("<img src='"+opt.elementsPath+"iconize.png' class='iconizeContainer'>");
				jQuery(this).find(".iconizeContainer:first").bind("click",function(){container.iconize(opt)});
			}
		}
		var fadeOnClose=jQuery.browser.mozilla || jQuery.browser.safari;
		jQuery(this).find(".buttonBar:first img").css({opacity:.5, cursor:"pointer"}).mouseover(function(){if (fadeOnClose)jQuery(this).fadeTo(200,1)}).mouseout(function(){if (fadeOnClose)jQuery(this).fadeTo(200,.5)});
	}
}
jQuery.fn.minimize = function (opt){
	var container=jQuery(this);
	if (jQuery(this).attr("minimized")=="false"){
		this.w = container.width();
		this.h = container.height();
		container.find(".containerTable:first").css("width","100%");
		container.find(".middle:first").fadeOut("fast",function(){container.css("height","")});
		jQuery(this).attr("minimized","true");
		container.find(".minimizeContainer:first").attr("src",opt.elementsPath+"max.png");
		container.resizable("destroy");
		jQuery.cookie('minimized', true, { path: '/', expires: 1}); 
		
	}else{
		container.find(".middle:first").fadeIn("slow",function(){container.css("height",container.find(".containerTable:first").height())});
		if (container.hasClass("resizable")) container.containerResize();
		jQuery(this).attr("minimized","false");
		container.find(".minimizeContainer:first").attr("src", opt.elementsPath+"min.png");
		jQuery.cookie('minimized', false, { path: '/', expires: 1}); 
	}
}
jQuery.fn.iconize = function (opt){
	return this.each (function ()
	{
		var container=jQuery(this);
		var browser_width =  jQuery(window).width() ;
		var browser_height =  jQuery(window).height() ;
		var marginRight = 0;//= jQuery.cookie('sidebar'); 
		
		container.attr("w",container.width());
		container.attr("h",container.height());
		container.attr("t",container.css("top"));
		container.attr("l",container.css("left"));
		container.resizable("destroy");
		if (!jQuery.browser.msie) {
			container.find(".containerTable:first").fadeOut("fast");
			container.animate({ top: jQuery.cookie('top_position_commentbox') ,left: browser_width + marginRight - 31},200);
		}else{
			container.find(".containerTable:first").hide();
			container.css({ top: "1px", left:browser_width + marginRight - 31});
		}
		container.append("<img src='"+opt.elementsPath+(container.attr("icon")?container.attr("icon"):"comments.png")+"' class='restoreContainer'>");
		container.find(".restoreContainer:first").bind("click",function(){
			if (!jQuery.browser.msie) {
				container.find(".containerTable:first").fadeIn("fast");
				container.animate({height:container.attr("h"), width:container.attr("w"),left:container.attr("l")},200);
			} else {
				container.find(".containerTable:first").show();
				container.css({height:container.attr("h"), width:container.attr("w"),left:container.attr("l")});
			}
			container.find(".restoreContainer:first").remove();
			if (container.hasClass("resizable")) container.containerResize();			
		});
	});
}



//jQuery('#accordion').accordion('activate',  2 );

// plugin definition
jQuery.fn.highlight = function() {
  // Our plugin implementation code goes here.
};

