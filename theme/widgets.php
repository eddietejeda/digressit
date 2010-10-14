<?php
global $blog_id;

if ( function_exists('register_sidebar') ) {



	if(WP_ALLOW_MULTISITE && ($blog_id == 1)){
		register_sidebar(array(
			'name' => 'Frontpage Sidebar',
			'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
	}

	register_sidebar(array(
		'name' => 'Mainpage Sidebar',
		'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	))	;

	register_sidebar(array(
		'name' => 'Single Sidebar',
		'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));

	register_sidebar(array(
		'name' => 'Page Sidebar',
		'before_widget' => '<div id="%1$s-sidebar" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));
	
	


	if(WP_ALLOW_MULTISITE && ($blog_id == 1)){
		//die('sdf');
		register_sidebar(array(
			'name' => 'Frontpage Content',
			'before_widget' => '<div id="%1$s-content" class="%2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h4>',
			'after_title' => '</h4>',
		));
	}

	register_sidebar(array(
		'name' => 'Mainpage Content',
		'before_widget' => '<div id="%1$s-content" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));

	register_sidebar(array(
		'name' => 'Single Content',
		'before_widget' => '<div id="%1$s-content" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));

	register_sidebar(array(
		'name' => 'Page Content',
		'before_widget' => '<div id="%1$s-content" class="%2$s">',
		'after_widget' => '</div>',
		'before_title' => '<h4>',
		'after_title' => '</h4>',
	));





	


}

?>