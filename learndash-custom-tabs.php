<?php
/*
Plugin Name: LearnDash Custom Tab
Description: Adds a custom tab to LearnDash courses.
Version: 1.0
Author: Abdul Moiz
Text Domain: learndash-custom-tab
Domain Path: /languages
*/

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register Custom Post Type
function register_custom_tab_cpt() {
    $labels = array(
        'name'                  => _x( 'Custom Tabs', 'Post Type General Name', 'learndash-custom-tab' ),
        'singular_name'         => _x( 'Custom Tab', 'Post Type Singular Name', 'learndash-custom-tab' ),
        'menu_name'             => __( 'Custom Tabs', 'learndash-custom-tab' ),
        'name_admin_bar'        => __( 'Custom Tab', 'learndash-custom-tab' ),
        'all_items'             => __( 'All Custom Tabs', 'learndash-custom-tab' ),
        'add_new_item'          => __( 'Add New Custom Tab', 'learndash-custom-tab' ),
        'add_new'               => __( 'Add New', 'learndash-custom-tab' ),
        'new_item'              => __( 'New Custom Tab', 'learndash-custom-tab' ),
        'edit_item'             => __( 'Edit Custom Tab', 'learndash-custom-tab' ),
        'update_item'           => __( 'Update Custom Tab', 'learndash-custom-tab' ),
        'view_item'             => __( 'View Custom Tab', 'learndash-custom-tab' ),
        'view_items'            => __( 'View Custom Tabs', 'learndash-custom-tab' ),
        'search_items'          => __( 'Search Custom Tab', 'learndash-custom-tab' ),
    );

    $args = array(
        'label'                 => __( 'Custom Tab', 'learndash-custom-tab' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor' ),
        'hierarchical'          => false,
        'public'                => false,
        'show_ui'               => true,
        'show_in_menu'          => 'learndash-lms',
        'menu_position'         => 5,
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => false,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => true,
        'publicly_queryable'    => false,
        'capability_type'       => 'page',
    );

    register_post_type( 'custom_tab', $args );
}
add_action( 'init', 'register_custom_tab_cpt', 0 );

// Add Meta Box
function custom_tab_add_meta_box() {
    add_meta_box(
        'custom_tab_meta_box',
        __( 'Custom Tab Settings', 'learndash-custom-tab' ),
        'custom_tab_meta_box_callback',
        'custom_tab',
        'side',
        'high'
    );
}
add_action( 'add_meta_boxes', 'custom_tab_add_meta_box' );

function custom_tab_meta_box_callback( $post ) {
    // Add a nonce field so we can check for it later.
    wp_nonce_field( 'custom_tab_save_meta_box_data', 'custom_tab_meta_box_nonce' );

    // Get the saved values.
    $selected_courses = get_post_meta( $post->ID, '_custom_tab_courses', true );
    $selected_users = get_post_meta( $post->ID, '_custom_tab_users', true );

    // Course Selection
    $courses = get_posts( array( 'post_type' => 'sfwd-courses', 'numberposts' => -1 ) );
    echo '<p><strong>' . __( 'Select Courses:', 'learndash-custom-tab' ) . '</strong></p>';
    echo '<select name="custom_tab_courses_option">';
    echo '<option value="all"' . ( $selected_courses == 'all' ? ' selected' : '' ) . '>' . __( 'All Courses', 'learndash-custom-tab' ) . '</option>';
    echo '<option value="selected"' . ( $selected_courses != 'all' ? ' selected' : '' ) . '>' . __( 'Selected Courses', 'learndash-custom-tab' ) . '</option>';
    echo '</select><br><br>';
    echo '<div id="custom_tab_courses" style="' . ( $selected_courses == 'all' ? 'display:none;' : '' ) . '">';
    foreach ( $courses as $course ) {
        echo '<input type="checkbox" name="custom_tab_courses[]" value="' . esc_attr( $course->ID ) . '" ' . ( is_array( $selected_courses ) && in_array( $course->ID, $selected_courses ) ? 'checked' : '' ) . '> ' . esc_html( $course->post_title ) . '<br>';
    }
    echo '</div>';

    // User Selection
    $users = get_users();
    echo '<p><strong>' . __( 'Select Users:', 'learndash-custom-tab' ) . '</strong></p>';
    echo '<select name="custom_tab_users_option">';
    echo '<option value="all"' . ( $selected_users == 'all' ? ' selected' : '' ) . '>' . __( 'All Users', 'learndash-custom-tab' ) . '</option>';
    echo '<option value="selected"' . ( $selected_users != 'all' ? ' selected' : '' ) . '>' . __( 'Selected Users', 'learndash-custom-tab' ) . '</option>';
    echo '</select><br><br>';
    echo '<div id="custom_tab_users" style="' . ( $selected_users == 'all' ? 'display:none;' : '' ) . '">';
    foreach ( $users as $user ) {
        echo '<input type="checkbox" name="custom_tab_users[]" value="' . esc_attr( $user->ID ) . '" ' . ( is_array( $selected_users ) && in_array( $user->ID, $selected_users ) ? 'checked' : '' ) . '> ' . esc_html( $user->display_name ) . '<br>';
    }
    echo '</div>';
}

// Save Meta Box Data
function custom_tab_save_meta_box_data( $post_id ) {
    // Check if our nonce is set.
    if ( ! isset( $_POST['custom_tab_meta_box_nonce'] ) ) {
        return;
    }

    // Verify that the nonce is valid.
    if ( ! wp_verify_nonce( $_POST['custom_tab_meta_box_nonce'], 'custom_tab_save_meta_box_data' ) ) {
        return;
    }

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check the user's permissions.
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Sanitize and save the selected courses.
    if ( isset( $_POST['custom_tab_courses_option'] ) ) {
        $selected_courses_option = sanitize_text_field( $_POST['custom_tab_courses_option'] );
        if ( $selected_courses_option == 'all' ) {
            update_post_meta( $post_id, '_custom_tab_courses', 'all' );
        } else {
            if ( isset( $_POST['custom_tab_courses'] ) ) {
                $selected_courses = array_map( 'sanitize_text_field', $_POST['custom_tab_courses'] );
                update_post_meta( $post_id, '_custom_tab_courses', $selected_courses );
            } else {
                delete_post_meta( $post_id, '_custom_tab_courses' );
            }
        }
    }

    // Sanitize and save the selected users.
    if ( isset( $_POST['custom_tab_users_option'] ) ) {
        $selected_users_option = sanitize_text_field( $_POST['custom_tab_users_option'] );
        if ( $selected_users_option == 'all' ) {
            update_post_meta( $post_id, '_custom_tab_users', 'all' );
        } else {
            if ( isset( $_POST['custom_tab_users'] ) ) {
                $selected_users = array_map( 'sanitize_text_field', $_POST['custom_tab_users'] );
                update_post_meta( $post_id, '_custom_tab_users', $selected_users );
            } else {
                delete_post_meta( $post_id, '_custom_tab_users' );
            }
        }
    }
}
add_action( 'save_post', 'custom_tab_save_meta_box_data' );

// Add Custom Tab to LearnDash
add_filter( 'learndash_content_tabs', function( $tabs = array(), $context = '', $course_id = 0, $user_id = 0 ) {
    // Get all custom tabs.
    $custom_tabs = get_posts( array( 'post_type' => 'custom_tab', 'numberposts' => -1 ) );

    foreach ( $custom_tabs as $custom_tab ) {
        $selected_courses = get_post_meta( $custom_tab->ID, '_custom_tab_courses', true );
        $selected_users = get_post_meta( $custom_tab->ID, '_custom_tab_users', true );

        if ( ( $selected_courses == 'all' || in_array( $course_id, $selected_courses ) ) && ( $selected_users == 'all' || in_array( $user_id, $selected_users ) ) ) {
            $tabs[ 'custom_tab_' . $custom_tab->ID ] = array(
                'id'      => 'custom_tab_' . $custom_tab->ID,
                'icon'    => 'ld-custom-tab-icon',
                'label'   => esc_html( $custom_tab->post_title ),
                'content' => '<p>' . wp_kses_post( $custom_tab->post_content ) . '</p>',
            );
        }
    }

    return $tabs;
}, 30, 4 );

// Enqueue custom styles and scripts
function learndash_custom_tab_enqueue_scripts() {
    wp_enqueue_style( 'learndash-custom-tab-style', plugins_url( '/css/custom-tab-style.css', __FILE__ ) );
    wp_enqueue_script( 'learndash-custom-tab-script', plugins_url( '/js/custom-tab-script.js', __FILE__ ), array( 'jquery' ), null, true );
}
add_action( 'wp_enqueue_scripts', 'learndash_custom_tab_enqueue_scripts' );

// Load plugin textdomain for translations.
function learndash_custom_tab_load_textdomain() {
    load_plugin_textdomain( 'learndash-custom-tab', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'learndash_custom_tab_load_textdomain' );
?>
