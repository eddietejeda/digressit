
jQuery(document).ready(function() {

	
	//jQuery.cookie('text_signature', null, { path: '/' , expires: 1} );				
	if(jQuery("#dynamic-sidebar").hasClass('sidebar-widget-auto-hide')){

		if(jQuery("#dynamic-sidebar").hasClass('sidebar-widget-position-right')){

			jQuery("#dynamic-sidebar").hover(function (e) {
				var t = setTimeout(function() {
					jQuery('#dynamic-sidebar').animate({ 
						right: "0px"
					}, 200 ); }, 500);
				jQuery(this).data('timeout', t);
			},function () {
			    clearTimeout(jQuery(this).data('timeout'));
				jQuery('#dynamic-sidebar').animate({ 
					right: "-260px"
				}, 200 );
			});
			
		}
		else{

			jQuery("#dynamic-sidebar").hover(function (e) {
				var t = setTimeout(function() {
					jQuery('#dynamic-sidebar').animate({ 
						left: "0px"
					}, 200 ); }, 500);
				jQuery(this).data('timeout', t);
			},function () {
			    clearTimeout(jQuery(this).data('timeout'));
				jQuery('#dynamic-sidebar').animate({ 
					left: "-260px"
				}, 200 );
			});
			
		}
	
	}
	
});

