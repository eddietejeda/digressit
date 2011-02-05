var AjaxResult = {};


jQuery(document).ready(function() {

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

	var msie=jQuery.browser.msie;
	var msie6=jQuery.browser.msie && jQuery.browser.version=="6.0";
	var msie7=jQuery.browser.msie && jQuery.browser.version=="7.0";
	var msie8=jQuery.browser.msie && jQuery.browser.version=="8.0";
	var safari=jQuery.browser.safari;
	var chrome=jQuery.browser.chrome;
	var mozilla=jQuery.browser.mozilla;
	var zi=10000;
	var on_load_selected_paragraph;
	var window_has_focus = true;
	var selected_comment_color = '#3d9ddd';
	var unselected_comment_color = '#DFE4E4';
	var browser_width = jQuery(window).width();
	var browser_height = jQuery(window).height();
	var request_time = 0;
	var request_time_delay = 500; // ms - adjust as you like

	
	if(jQuery('.tabs').length){
		jQuery('.tabs').generate_tabs();
		
	}


	//jQuery('#dynamic-sidebar').effect("bounce", { direction: 'right', times:1 }, 1500);



	AjaxResult.live_content_search = function(data) {
		jQuery('#live-content-search-result').html(data.message);
		jQuery('#live-content-search-result').fadeIn();
	}
	
	
	jQuery('#live-content-search').focus(function(){
		if(jQuery('#live-content-search').val() == 'Search'){
			jQuery('#live-content-search').val('');
		}
	});
	
	
	AjaxResult.live_comment_search = function(data) {
		jQuery('.comment').hide();
		
		
		//jQuery('.comment-text').clone().find('span').replaceWith(function() { return this.innerHTML; }).end().html();
		
		jQuery(".comment").highlight(jQuery('#live-comment-search').val(), "highlight-class");
		
		for (var i in data.message) {  
			jQuery('#comment-' + current_blog_id + '-' + data.message[i]).show();
		}  
	}
	
	jQuery('#live-comment-search').focus(function(){
		if(jQuery('#live-comment-search').val() == 'Search'){
			jQuery('#live-comment-search').val('');
		}
	});





	
	jQuery('.submit, .lightbox-submit').click(function(e){
		if(jQuery(e.target).hasClass('ajax')){
			//return false;
		}
		else{
			jQuery(e.target).addClass('disabled');
		}
	});
	
	
	jQuery('.input').keypress(function(e){
		//alert(e.target);
	})

	


	jQuery('.ajax').live('click', function(e) {		
		if(jQuery(this).hasClass('disabled') || jQuery(this).hasClass('button-disabled')){
			jQuery(this).css('color', '#DFE4E4');
			return;
		}
		
		var form = this;
		form = jQuery(this).parentsUntil('form').parent();		
		var form_id = jQuery(form).attr('id');

		//alert(function_name);

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
	
	jQuery('.ajax-auto-submit input').live('keyup', function(e) {
		if(e.keyCode == 13 && jQuery(this).val().length > 0){
			//alert('sf');
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
	


	
	jQuery('.ajax-live').live('keyup', function(e) {

		if(request_time) {
			clearTimeout(request_time)
		}
		
		request_time = setTimeout(function(obj){
			if(jQuery(obj).hasClass('disabled') || jQuery(this).hasClass('button-disabled')){
				jQuery(obj).css('color', '#DFE4E4');
				return true;
			}
			
			if(!jQuery(obj).attr('class').toSting().length){
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
	
	jQuery('.ajax-simple').click(function (e) {
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

		//alert(function_name);
		jQuery('.' + function_name).css('background', 'invert');
		jQuery(this).css('background', '#ddd');
		var function_parameters = parseGetVariables( jQuery(this).attr('value'));
		
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
	});
	
	jQuery('.button-green').hover(function(){
		
		jQuery(this).css('cursor', 'pointer');
		
		
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
		//alert(name_values);
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
	

	if(is_single){
		jQuery('#commentbox').position_main_elements();
	}
	
	
    jQuery(window).scroll(function () { 
	
		if(is_single){
			jQuery('#commentbox').position_main_elements();
		}
		
    });


	jQuery(window).resize(function(){
		if(is_single){
			jQuery('#commentbox').position_main_elements();
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
});



jQuery.fn.extend({
	generate_tabs: function(type, options) {
		//Default Action


		jQuery('ul.tabs').each(function(item){
			var tab_id = jQuery(this).attr('id');			
			//alert(tab_id);
			jQuery("#" +tab_id +" + .tab-container .tab-content").hide();					//Hide all content
			jQuery("#" +tab_id +"  li:first").addClass("active").show();	//Activate first tab
			jQuery(".tab-container  .tab-content:first").show();				//Show first tab content
		});

		//On Click Event
		jQuery("ul.tabs li").click(function() {

			var tab_id = jQuery(this).parent().attr('id');		


			jQuery("#"+tab_id+ " li").removeClass("active"); //Remove any "active" class
			jQuery(this).addClass("active"); //Add "active" class to selected tab

			jQuery("#"+tab_id+ " + .tab-container .tab-content").hide(); //Hide all tab content
			jQuery("." +tab_id+" .tab-content").hide(); //Hide all tab content

			
			var activeTab = jQuery(this).find("a").attr("href"); //Find the rel attribute value to identify the active tab + content
			jQuery(activeTab).show();
			//alert(activeTab);
			return false;
		});
	},
	

	position_main_elements: function() {

		var browser_width = jQuery(window).width();
		var browser_height = jQuery(window).height();
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

		var msie=jQuery.browser.msie;
		var msie6=jQuery.browser.msie && jQuery.browser.version=="6.0";
		var msie7=jQuery.browser.msie && jQuery.browser.version=="7.0";
		var msie8=jQuery.browser.msie && jQuery.browser.version=="8.0";
		var safari=jQuery.browser.safari;
		var chrome=jQuery.browser.chrome;
		var mozilla=jQuery.browser.mozilla;


		var default_top = parseInt(jQuery('#content').position().top);
		var top =  default_top  + parseInt(jQuery(window).scrollTop());
		var min_browser_height = (browser_height > 300) ? browser_height : 300; 
		var new_commentbox_height = ((browser_height - default_top - 150) < 370) ? 370 : (browser_height - default_top - 150);
		var commentbox_top = jQuery(jQuery(".entry").get(0)).offset().top;

		jQuery('#commentbox').css('top',  commentbox_top + 'px' );
		jQuery('#commentbox').css('height', new_commentbox_height + 'px');


		var left = parseInt(jQuery('#content').offset().left) + parseInt( jQuery(jQuery('.entry').get(0)).width() )  + 95;

		if(safari || chrome){
			//left = left + 210;
		}


		var sidebar_fix_point = parseInt(jQuery("#header").outerHeight())  + parseInt(jQuery("#header").css('margin-top'));
		var commentbox_fix_point = commentbox_top;

		if(parseInt(jQuery(window).scrollTop()) > sidebar_fix_point){
			jQuery("#dynamic-sidebar").css('position',  'fixed');			
			jQuery("#dynamic-sidebar").css('top',  '0px');			
		}
		else{
			jQuery("#dynamic-sidebar").css('position',  'absolute');
			jQuery("#dynamic-sidebar").css('top',  parseInt(jQuery("#header").outerHeight()));			
		}

		if(parseInt(jQuery(window).scrollTop()) > commentbox_fix_point - parseInt(jQuery("#header").outerHeight()) ){

			jQuery("#commentbox-header").css('position',  'fixed');			
			jQuery("#commentbox-header").css('top',  parseInt(jQuery("#header").outerHeight()));			

			jQuery("#commentbox").css('position',  'fixed');			
			jQuery("#commentbox").css('top',  parseInt(jQuery("#header").outerHeight())  + parseInt(jQuery("#commentbox-header").outerHeight()) );			
		}
		else{
			jQuery("#commentbox-header").css('position',  'absolute');
			jQuery("#commentbox-header").css('top',  commentbox_top + 'px');			
			
			jQuery("#commentbox").css('position',  'absolute');
			jQuery("#commentbox").css('top',  (commentbox_top + parseInt(jQuery("#commentbox-header").outerHeight())) + 'px');			
		}
		
		
		//stick the footer at the bottom of the page if we're on an iPad/iPhone due to viewport/page bugs in mobile webkit
		if(navigator.platform == 'iPad' || navigator.platform == 'iPhone' || navigator.platform == 'iPod')
		{
			 //jQuery("#footer").css("position", "static");
		};


		
		
		jQuery('#commentbox,#commentbox-header').css('left', left + 'px');
		jQuery('#commentbox,#commentbox-header').css('display', 'block');
		

	}	
	
});





function commentbox_closed_state(){
	//jQuery('#respond').appendTo('#comments-toolbar');
	jQuery('#comment_parent').val(0);
	jQuery('#comment').val('Discuss Here...');
	jQuery('#commentbox').css('overflow-y', 'scroll');
	//jQuery('#submit-comment').css('display', 'none');

	jQuery('#comments-toolbar').show();
}

function commentbox_reply_state(){
	
	//alert(selected_comment_id);
	var selected_comment_id = jQuery.cookie('selected_comment_id');				
	var selected_blog_id = jQuery.cookie('selected_blog_id');				
	
	//alert(selected_comment_id);
	var reply_box = '#comment-'+ selected_blog_id + '-' +selected_comment_id + ' .comment-respond';
	
	//alert(reply_box);
	//jQuery(reply_box).hide();
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

