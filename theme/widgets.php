<?php
global $blog_id;

if ( function_exists('register_sidebar') ) {



	if(WP_ALLOW_MULTISITE && ($blog_id == 1)){
		register_sidebar(array(
			'name' => 'Frontpage Sidebar',
			'id' => 'frontpage-sidebar',		
			'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
	}

	register_sidebar(array(
		'name' => 'Mainpage Sidebar',
		'id' => 'mainpage-sidebar',		
		'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	))	;

	register_sidebar(array(
		'id' => 'single-sidebar',		
		'name' => 'Single Sidebar',
		'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));

	register_sidebar(array(
		'id' => 'page-sidebar',		
		'name' => 'Page Sidebar',
		'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));
	
	


	if(WP_ALLOW_MULTISITE && ($blog_id == 1)){
		register_sidebar(array(
			'id' => 'frontpage-content',		
			'name' => 'Frontpage Content',
			'before_widget' => '<div id="%1$s-content" class="%2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
	}

	register_sidebar(array(
		'id' => 'mainpage-content',		
		'name' => 'Mainpage Content',
		'before_widget' => '<div id="%1$s-content" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));

	register_sidebar(array(
		'id' => 'single-content',		
		'name' => 'Single Content',
		'before_widget' => '<div id="%1$s-content" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));

	register_sidebar(array(
		'id' => 'page-content',		
		'name' => 'Page Content',
		'before_widget' => '<div id="%1$s-content" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));





	


}

?>