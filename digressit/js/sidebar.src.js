jQuery(document).ready(function(){
	
	var marginRight = jQuery.cookie('sidebar'); 
	
	if(marginRight == null)
	{
		jQuery('#sidebar').css('marginRight',   '0px' );
	}
	else
	{
		jQuery('#sidebar').css('marginRight', marginRight + 'px' );
	}
	

	jQuery.fn.blindToggle = function(speed, easing, callback) {
		var width = this.width() + parseInt(this.css('paddingLeft')) + parseInt(this.css('paddingRight'));
		var marginRight = parseInt(this.css('marginRight'));		
		this.animate({marginRight: marginRight < 0 ? 0 : -width}, speed, easing, callback);
		
	};


	var save_sidebar = function(){
		jQuery.cookie('sidebar', null);	
		jQuery('#sidebar').css('marginRight');
		jQuery.cookie('sidebar', parseInt(jQuery('#sidebar').css('marginRight')), { path: '/', expires: 7} );
	}
			
	

	jQuery('#togglerbutton').click(function() {
		jQuery('#sidebar').blindToggle('slow', null, save_sidebar);
	});

});
