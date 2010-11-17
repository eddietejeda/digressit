window.onload = function() {
	settings = {
		tl: { radius: 10 },
		tr: { radius: 10 },
		bl: { radius: 10 },
		br: { radius: 10 },
		antiAlias: true,
		autoPad: true
	}
	var myBoxObject = new curvyCorners(settings, "rounded");
	myBoxObject.applyCornersToAll();
}

jQuery(document).ready(function() {


	jQuery(".lightbubble").click(function (e) {


		if(jQuery(e.target).hasClass('button-disabled') || jQuery(e.target).hasClass('disabled')){
			return false;
		}


		var target = e.target;

		//var top = jQuery(target).offset().top;
		//var left = jQuery(target).offset().left;
	
		var lightbubble_name = jQuery(target).attr('class').split(' ');
	
		var lightbubble, i;
		for(i = 0; i < lightbubble_name.length; i++){
		
			if(lightbubble_name[i] == 'lightbubble'){
				lightbubble = '#' + lightbubble_name[i+1];
				break;				
			}
		}
		//alert(lightbubble);

		jQuery(lightbubble).appendTo(jQuery(this));
		jQuery(lightbubble).show();
		//jQuery('body').openlightbubble(lightbubble);
	
	});
});
