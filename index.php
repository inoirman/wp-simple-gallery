<?php
/*
Plugin Name: Simple Gallery
Description: Простая фотогалерея, отображаемая по шорткоду.
Version: 1.0.0
Text Domain: kiadev
*/

add_action('init', 'register_gallery_type');

function register_gallery_type()
{
    register_post_type('photoreview', array(
		'labels'             => array(
			'name'                      => 'Фотоотзывы', // Основное название типа записи
			'singular_name'             => 'Фотоотзыв', // отдельное название записи типа Book
			'add_new'                   => 'Добавить новый',
            'add_new_item'              => 'Добавить новый',
            'new_item'                  => 'Новый(ая)',
            'edit_item'                 => 'Редактировать',
            'view_item'                 => 'Просмотреть',
            'update_item'               => 'Изменить',
            'not_found'                 => 'Не найдено',
            'search_items'              => 'Искать',
            'add_or_remove_items'       => 'Добавить или удалить',
			'menu_name'                 => 'Фотоотзывы'

		  ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => true,
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
        'menu_position'      => 22,
        'menu_icon'          => 'dashicons-images-alt2',
		'supports'           => array('title','thumbnail')
	) );
    
}

function custom_columns( $columns ) {
    $columns = array(
        'cb' => '<input type="checkbox" />',
        'featured_image' => 'Image',
        'title' => 'Title',
        'date' => 'Date'
     );
    return $columns;
}
add_filter('manage_posts_columns' , 'custom_columns');

function custom_columns_data( $column, $post_id ) {
    switch ( $column ) {
    case 'featured_image':
        the_post_thumbnail( 'thumbnail' );
        break;
    }
}
add_action( 'manage_posts_custom_column' , 'custom_columns_data', 10, 2 ); 

function kiadev_load_scripts(){

	wp_enqueue_style( 'lightgallery-css', plugin_dir_url( __FILE__ ) . 'assets/css/lightgallery.min.css', array(), '1.6.12', 'all' );
	
    wp_enqueue_script( 'lightgallery-js', plugin_dir_url( __FILE__ ) . 'assets/js/lightgallery.min.js', array('jquery'), '1.6.12', true );  
    wp_enqueue_script( 'lg-thumbnail-js', plugin_dir_url( __FILE__ ) . 'assets/js/lg-thumbnail.min.js', array('jquery'), '1.6.12', true );
    wp_enqueue_script( 'lg-fullscreen-js', plugin_dir_url( __FILE__ ) . 'assets/js/lg-fullscreen.min.js', array('jquery'), '1.6.12', true );

    wp_enqueue_script( 'app-lightgallery-js', plugin_dir_url( __FILE__ ) . 'assets/js/app.js', array('jquery'), '1.0', true );

}
add_action( 'wp_enqueue_scripts', 'kiadev_load_scripts' );

function shortcode_output_func( $atts ) {
	

    ob_start();
    
    $args = array(
        'post_type' => 'photoreview' ,
        'orderby' => 'date' ,
        'order' => 'DESC' 
    ); 

    
    $q = new WP_Query($args);
    
    echo '<div id="aniimated-thumbnials">';
    $_i = 0;
    $rnd = rand();
    if ( $q->have_posts() ) { 
        while ( $q->have_posts() ) {
            $q->the_post();
                
            echo '<a href="'. get_the_post_thumbnail_url($post->ID, "full") .'" data-sub-html="'. get_the_title() .'">';
            echo '<img src="'. get_the_post_thumbnail_url($post->ID, "medium") .'" />';
            echo '</a>';

                if ($_i == 8) {
                    echo '<div class="collapse" id="WatchMore_'. $rnd .'">';
                }
                $_i++;
        }
    }
    echo '</div>';
    if ($_i > 8) {
        echo '<div class="text-center">
    <a id="ReadButtonOn_'. $rnd .'" class="btn btn-link btn-lg" role="button" data-toggle="collapse" href="#WatchMore_'. $rnd .'" aria-expanded="false" aria-controls="collapseExample" onclick="changeModal('. $rnd .')">Показать все</a>
    <br>
    <a class="btn btn-link btn-lg" role="button" data-toggle="collapse" href="#WatchMore_'. $rnd .'" aria-expanded="false" aria-controls="collapseExample" onclick="changeModal('. $rnd .')" style="margin-top:-20px;">
     <big><span id="ReadIcon_'. $rnd .'" class="glyphicon glyphicon-chevron-down" aria-hidden="true"></big></span>
    </a>
    <br>
    <a id="ReadButtonOff_'. $rnd .'" class="hide" role="button" data-toggle="collapse" href="#WatchMore_'. $rnd .'" aria-expanded="false" aria-controls="collapseExample" onclick="changeModal('. $rnd .')" style="margin-top:-20px;">Скрыть</a>
   </div>';
    // echo '</div>';
    }
    
    
    return ob_get_clean();
}

add_shortcode( 'photo_review', 'shortcode_output_func' );