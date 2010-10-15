jQuery.fn.openlightbox = function (lightbox){
	if(jQuery(lightbox).length){
		jQuery('.lightbox-content').hide();
		var browser_width = jQuery(window).width();
		var browser_height = jQuery(window).height();
		var body_width = jQuery('#wrapper').width();
		var body_height = jQuery('#wrapper').height();


		jQuery('.lightbox-submit').removeClass('disabled');

		jQuery('.lightbox-transparency').css('width', body_width  + 'px');
		jQuery('.lightbox-transparency').css('height', ( body_height + 70 )+ 'px');
		jQuery('.lightbox-transparency').fadeTo(0, 0.20);				

		var left = (parseInt(browser_width) -  parseInt((jQuery(lightbox).width())))/2.5;
		var top = (parseInt(browser_height) -  parseInt((jQuery(lightbox).height())))/3;
	
		if(top < 45){
			top = 45;			
		}
		if(left < 100){
			left = 100;
		}
		
		jQuery(lightbox).css('left', left);			
		jQuery(lightbox).css('top', top);			
		jQuery(lightbox).show();
		
		jQuery('input[type=button]').attr('disabled', false);
		jQuery('input[type=submit]').attr('disabled', false);
		jQuery('input[type=text]').attr('readonly', '');
		jQuery('select').attr('disabled', false);
		jQuery('textarea').attr('readonly', '');
		

		if(jQuery(lightbox + ' .lightbox-delay-close').length){
			var t = setTimeout(function() {
				jQuery("body").closelightbox();
 			}, 1000);
			jQuery(this).data('timeout', t);			
		}		
	}
	else{
		//console.log(lightbox + ' not found ');		
	}

}




jQuery.fn.closelightbox = function (){
	jQuery('.lightbox-content').hide();
	jQuery('.lightbox-transparency').css('width', 0);
	jQuery('.lightbox-transparency').css('height', 0);
	document.location.hash.length = '';
}

jQuery.fn.displayerrorslightbox = function (data){
	if(data.status == 0){
		var lightbox = '#lightbox-generic-response';
		jQuery(lightbox + ' > p').html(data.message);
		jQuery('body').openlightbox(lightbox);	
	}
}






jQuery(document).ready(function() {

	if (document.location.hash.length) {
		var lightbox = '#lightbox-' + document.location.hash.substr(1);
		jQuery('body').openlightbox(lightbox);
	}	
	

	/* we don't want error messages being linked */
	if (document.location.hash != '#lightbox-no-ie6-support' && jQuery('#lightbox-no-ie6-support').length ) {
		jQuery('body').openlightbox('#lightbox-no-ie6-support');

	}	
	
	
	
	jQuery("#search_context").change(function (e) {		
		jQuery("#searchform").attr('action', jQuery("#search_context option:selected").val());
	});
	
	
	jQuery('.close').click(function(){
		jQuery(this).parent().hide();		
		jQuery('#block-access').hide();
	});
	
	jQuery(".insert-link").click(function (e) {
		var name = jQuery("#link_name").val();
		var link = jQuery("#link_url").val();
		jQuery("#comment").val(jQuery("#comment").val() + '<a href="'+link+'">'+name+'</a>');
		jQuery("body").closelightbox();
		
	});
	
	
    jQuery(".lightbox").click(function (e) {

		if(jQuery(e.target).hasClass('button-disabled') || jQuery(e.target).hasClass('disabled')){
			return false;
		}

		var target = e.target;		
		var lightbox_name = jQuery(target).attr('class').split(' ');
		
		var lightbox, i;
		for(i = 0; i < lightbox_name.length; i++){
			if(lightbox_name[i] == 'lightbox'){
				lightbox = '#' + lightbox_name[i+1];
				break;				
			}
		}
		
		jQuery('body').openlightbox(lightbox);
		
	});
	
	

	
	jQuery('.lightbox-content input').keyup(function(event) {


		if (event.keyCode == '13') {
			//alert(event.keyCode);
			//alert(jQuery(this).attr('class'));
			if(jQuery(this).hasClass('ajax')){
				//alert('ajax');
		  		/* UNDO COMMENT: */
				//jQuery(this).add('.lightbox-submit').click();
			}
			else{
				//alert('submit');
		  		/* UNDO COMMENT: */
		  		//jQuery(this).parent().submit();
			}
		}
	});


    jQuery(".lightbox-close, .lightbox-submit-close").click(function (e) {
		jQuery('body').closelightbox();
	});
	
	/*
    jQuery(".lightbox-submit-close").click(function (e) {

		jQuery('body').closelightbox();
	});
	*/
	
	
    jQuery(".lightbox-submit").click(function (e) {

		//alert(jQuery(this).attr('class'));
		if(jQuery(this).hasClass('ajax')){
	  		//alert(jQuery(this).parent());
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

});


