/*
window.onload = function () {
    var r = Raphael("holder");
    r.g.txtattr.font = "12px 'Fontin Sans', Fontin-Sans, sans-serif";

    r.g.text(160, 35, "Static Pie Chart").attr({"font-size": 12});

    r.g.piechart(160, 120, 75, [75, 25]);
};

*/

jQuery(document).ready(function() {

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

	
	
	
	

	
	
	

});