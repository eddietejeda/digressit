jQuery(document).ready(function() {




	var msie=jQuery.browser.msie;
	var msie6=jQuery.browser.msie && jQuery.browser.version=="6.0";
	var msie7=jQuery.browser.msie && jQuery.browser.version=="7.0";
	var msie8=jQuery.browser.msie && jQuery.browser.version=="8.0";
	var safari=jQuery.browser.safari;
	var mozilla=jQuery.browser.mozilla;
	var zi=10000;
	var on_load_selected_paragraph;
	var window_has_focus = true;



	jQuery("#dynamic-sidebar").hover(function () {
	
			var index = jQuery('#dynamic-sidebar').index(this);
			var item = jQuery('#dynamic-sidebar').get(index);			
			jQuery(item).animate({ 
				left: "0px",
			}, 100 );
		
		},function () {
	
			var index = jQuery('#dynamic-sidebar').index(this);
			var item = jQuery('#dynamic-sidebar').get(index);			
			jQuery(item).animate({ 
				left: "-210px",
			}, 100 );

		}
	);


	jQuery.preLoadImages("menu-bar.png", "../images/menu-bar.png");

	
	function position_main_elements(){

		var default_top = 200;
	
		var top =  default_top  + parseInt(jQuery(window).scrollTop());

		jQuery('#comments-toolbar').css('top',   '100px');
		jQuery('#commentbox').css('top', default_top + 'px');
		

		if(parseInt(jQuery(window).scrollTop()) > 150){
			var top = parseInt(jQuery(window).scrollTop());	
			jQuery("#dynamic-sidebar").css('top',  0);			
			jQuery("#dynamic-sidebar").css('position',  'fixed');			
		}
		else{
			var top = 100;			
			jQuery("#dynamic-sidebar").css('position',  'absolute');
			jQuery("#dynamic-sidebar").css('top',  top+'px');
		}
	}
		
		
	//jQuery('#commentwindow').jScrollPane();
	//jQuery('#commentwindow').jScrollPane({showArrows:true});
	//$('#pane3, #pane4').jScrollPane({scrollbarWidth:20, scrollbarMargin:10});
    
		

    jQuery(window).scroll(function () { 
		if(msie6 || msie7 ){
			jQuery('#sidebar, #commentbox').css({position:"absolute"});
			position_main_elements();
		}
    });

	jQuery(window).resize(function(){
		position_main_elements();
	});

	position_main_elements();





/*
	jQuery('#commentwindow').mousewheel(function(event, delta) {
		return false; // prevent default
	});
*/

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


	jQuery('#discuss-form').focus(function(event)  {
		var target = event.target;		
		jQuery(target).animate({height: '100px'}, 'fast');

	});

	jQuery('#discuss-form').blur(function(event)  {
		var target = event.target;		
		jQuery(target).animate({height: '10px'}, 'fast');

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



	jQuery('.login-button').click(function(){
		jQuery("#loginbox").show();
	});


	
	var selected = jQuery.cookie('text_signature');				

	if(selected){
		var item = jQuery('.commenticonbox').get(selected);
		jQuery(item).css('background-color', '#3d9ddd');	
	}
	
	
	jQuery('.textblock').hover(function(e){

		var index = jQuery('.textblock').index(this);
		var item = jQuery('.commenticonbox').get(index);
		jQuery(item).css('background-color', '#3d9ddd');	
	
		var selected = jQuery.cookie('text_signature');				

	
	},function (e){
		
		
		var current = parseInt(jQuery('.textblock').index(this)) + 1;	
		var selected = jQuery.cookie('text_signature');				

		jQuery('.commenticonbox').css('background-color', '#DFE4E4');				

		var item = jQuery('.commenticonbox').get(selected-1);
		jQuery(item).css('background-color', '#3d9ddd');	

	});


	jQuery('#comment').click(function(e){
		
		jQuery(this).css('height', '100px');

		jQuery(this).animate({ 
			height: "100px",
		}, 100 );

		jQuery('#submit').show();
		
	});

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


	jQuery('.commenticonbox').click(function(e){


		var paragraphnumber = parseInt(jQuery('.commenticonbox').index(this)) + 1;
		var item = jQuery('.commenticonbox').get(parseInt(jQuery('.commenticonbox').index(this)));


		if(selected == paragraphnumber){
			jQuery('.commenticonbox').css('background-color', '#DFE4E4');				
			jQuery.cookie('text_signature', null, { path: '/', expires: 1} );				
			//alert('resetting');
		}
		
		
		jQuery(item).css('background-color', '#3d9ddd');	

		jQuery.cookie('text_signature', paragraphnumber, { path: '/', expires: 1} );				
		
		jQuery(".comment").hide();
		jQuery("#comments-toolbar").show();
		
		jQuery("#commentbox").css('background', 'none');


		for ( var i in commment_text_signature ){			
			if(parseInt(paragraphnumber) == parseInt(commment_text_signature[i])){
				var comment_id = "#comment-" + i;
				jQuery(comment_id).show();
			}
		}		

	});

	

	jQuery('.comment').hover(function(e){
		
		var current = parseInt(jQuery('.comment').index(this));	
		var item = jQuery('.comment-reply').get(current);
	
		jQuery(item).css('background', '#3D9DDD');		
		
	}, function(e){
		
		var current = parseInt(jQuery('.comment').index(this));	
		var item = jQuery('.comment-reply').get(current);
		jQuery(item).css('background', 'none');	
	
	});

	

	
	jQuery('.comments-toolbar-icon').hover(function(e){
		var target = e.target;
		var toolbar = jQuery(target).attr('id');
		
		jQuery('#' + toolbar + '-tooltip').fadeIn('fast');
		
		},
		function (e){
			jQuery('.comments-toolbar-tooltip').fadeOut('fast');
			
		}
	);


    jQuery(".lightbox").click(function (e) {
	
		var target = e.target;
		var lightbox = '#' + jQuery(target).attr('id') + '-lightbox';
		
		alert(lightbox);
	
		jQuery("body").append('<div class="lightbox-transparent-shading"></div>'); 
	
		if(jQuery(lightbox).length){
			
			jQuery(lightbox).fadeIn('fast');
		}
	});



	
    jQuery("#search-button").click(
      function () {
	    jQuery("#search-button ul").show('slow');
      }
    );


    jQuery("#search-button ul").hover(
      function () {
	    jQuery("#search-button ul").css('z-index', '500');
	    jQuery("#search-button ul").children().show();
      }, 
      function () {
	    jQuery("#search-button ul").hide('slow');
      }
    );


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


			//alert(jQuery(e.target).attr('class'));

			if(jQuery(e.target).attr('class') == 'paragraph_number_large' || jQuery(e.target).attr('class') == 'read_num_comment_general'){
				jQuery('#comment_contents > .containerBody').scrollTo( jQuery('#respond'), 1000);								
			}
			else{
				jQuery('#comment_contents > .containerBody').scrollTo( jQuery('#comment-block-' + (paragraphnumber)), 1000);				
			}
			document.location.hash = "#" +(paragraphnumber);
			jQuery(this).recover();

		}
	});




	jQuery("#comment").focus(function() {
		if (jQuery('#comment_survey').length ) {
			//alert('showin');
			jQuery('#comment_survey').fadeIn();
		    jQuery('input[type=submit]', this).attr('disabled', 'disabled');
			jQuery('#block-access').show();
		}		
	});


	
	
	jQuery('.close').click(function(){
		jQuery(this).parent().fadeOut();
		jQuery('#block-access').hide();
		
	});



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

		
	
});


(function($) {
  var cache = [];
  // Arguments are image paths relative to the current page.
  $.preLoadImages = function() {
    var args_len = arguments.length;
    for (var i = args_len; i--;) {
      var cacheImage = document.createElement('img');
      cacheImage.src = arguments[i];
      cache.push(cacheImage);
    }
  }
})(jQuery)