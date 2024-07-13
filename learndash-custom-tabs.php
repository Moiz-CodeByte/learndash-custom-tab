<?php
/*
Plugin Name: LearnDash Custom Tab
Description: Adds a custom tab to LearnDash courses, lessons, topics, and quizzes.
Version: 1.3
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
        'name'          => _x( 'Custom Tabs', 'Post Type General Name', 'learndash-custom-tab' ),
        'singular_name' => _x( 'Custom Tab', 'Post Type Singular Name', 'learndash-custom-tab' ),
        'menu_name'     => __( 'Custom Tabs', 'learndash-custom-tab' ),
        'all_items'     => __( 'All Custom Tabs', 'learndash-custom-tab' ),
        'add_new_item'  => __( 'Add New Custom Tab', 'learndash-custom-tab' ),
        'new_item'      => __( 'New Custom Tab', 'learndash-custom-tab' ),
        'edit_item'     => __( 'Edit Custom Tab', 'learndash-custom-tab' ),
        'view_item'     => __( 'View Custom Tab', 'learndash-custom-tab' ),
    );

    $args = array(
        'label'         => __( 'Custom Tab', 'learndash-custom-tab' ),
        'labels'        => $labels,
        'supports'      => array( 'title', 'editor' ),
        'public'        => false,
        'show_ui'       => true,
        'show_in_menu'  => 'learndash-lms',
        'menu_position' => 5,
        'capability_type' => 'page',
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
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'custom_tab_add_meta_box' );

function custom_tab_meta_box_callback( $post ) {
    wp_nonce_field( 'custom_tab_save_meta_box_data', 'custom_tab_meta_box_nonce' );

    // Retrieve saved values
    $selected_users = get_post_meta( $post->ID, '_custom_tab_users', true );
    $display_on = get_post_meta( $post->ID, '_custom_tab_display_on', true );
    $selected_courses = get_post_meta( $post->ID, '_custom_tab_courses', true );
    $selected_lessons = get_post_meta( $post->ID, '_custom_tab_lessons', true );
    $selected_topics = get_post_meta( $post->ID, '_custom_tab_topics', true );
    $selected_quizzes = get_post_meta( $post->ID, '_custom_tab_quizzes', true );
    $icon_class = get_post_meta( $post->ID, '_custom_tab_icon_class', true );

    // User Selection
    echo '<p><strong>' . __( 'Display To/Select Users:', 'learndash-custom-tab' ) . '</strong></p>';
    echo '<select name="custom_tab_users[]" multiple class="select2">';
    echo '<option value="all"' . ( $selected_users == 'all' ? ' selected' : '' ) . '>' . __( 'All Users', 'learndash-custom-tab' ) . '</option>';
    foreach ( get_users() as $user ) {
        echo '<option value="' . esc_attr( $user->ID ) . '" ' . ( is_array( $selected_users ) && in_array( $user->ID, $selected_users ) ? 'selected' : '' ) . '>' . esc_html( $user->display_name ) . '</option>';
    }
    echo '</select><br><br>';

    // Display On Selection
    echo '<p><strong>' . __( 'Display On:', 'learndash-custom-tab' ) . '</strong></p>';
    echo '<select name="custom_tab_display_on" class="select2">';
    foreach ( ['courses', 'lessons', 'topics', 'quizzes'] as $option ) {
        echo '<option value="' . $option . '"' . ( $display_on == $option ? ' selected' : '' ) . '>' . ucfirst($option) . '</option>';
    }
    echo '</select><br><br>';

    // Course Selection
    echo '<p><strong>' . __( 'Select Courses:', 'learndash-custom-tab' ) . '</strong></p>';
    echo '<select name="custom_tab_courses[]" multiple class="select2">';
    echo '<option value="all"' . ( $selected_courses == 'all' ? ' selected' : '' ) . '>' . __( 'All Courses', 'learndash-custom-tab' ) . '</option>';
    foreach ( get_posts( array( 'post_type' => 'sfwd-courses', 'numberposts' => -1 ) ) as $course ) {
        echo '<option value="' . esc_attr( $course->ID ) . '" ' . ( is_array( $selected_courses ) && in_array( $course->ID, $selected_courses ) ? 'selected' : '' ) . '>' . esc_html( $course->post_title ) . '</option>';
    }
    echo '</select><br><br>';

    // Lesson Selection
    echo '<p><strong>' . __( 'Select Lessons:', 'learndash-custom-tab' ) . '</strong></p>';
    echo '<select name="custom_tab_lessons[]" multiple class="select2">';
    echo '<option value="all"' . ( $selected_lessons == 'all' ? ' selected' : '' ) . '>' . __( 'All Lessons', 'learndash-custom-tab' ) . '</option>';
    foreach ( get_posts( array( 'post_type' => 'sfwd-lessons', 'numberposts' => -1 ) ) as $lesson ) {
        echo '<option value="' . esc_attr( $lesson->ID ) . '" ' . ( is_array( $selected_lessons ) && in_array( $lesson->ID, $selected_lessons ) ? 'selected' : '' ) . '>' . esc_html( $lesson->post_title ) . '</option>';
    }
    echo '</select><br><br>';

    // Topic Selection
    echo '<p><strong>' . __( 'Select Topics:', 'learndash-custom-tab' ) . '</strong></p>';
    echo '<select name="custom_tab_topics[]" multiple class="select2">';
    echo '<option value="all"' . ( $selected_topics == 'all' ? ' selected' : '' ) . '>' . __( 'All Topics', 'learndash-custom-tab' ) . '</option>';
    foreach ( get_posts( array( 'post_type' => 'sfwd-topic', 'numberposts' => -1 ) ) as $topic ) {
        echo '<option value="' . esc_attr( $topic->ID ) . '" ' . ( is_array( $selected_topics ) && in_array( $topic->ID, $selected_topics ) ? 'selected' : '' ) . '>' . esc_html( $topic->post_title ) . '</option>';
    }
    echo '</select><br><br>';

    // Quiz Selection
    echo '<p><strong>' . __( 'Select Quizzes:', 'learndash-custom-tab' ) . '</strong></p>';
    echo '<select name="custom_tab_quizzes[]" multiple class="select2">';
    echo '<option value="all"' . ( $selected_quizzes == 'all' ? ' selected' : '' ) . '>' . __( 'All Quizzes', 'learndash-custom-tab' ) . '</option>';
    foreach ( get_posts( array( 'post_type' => 'sfwd-quiz', 'numberposts' => -1 ) ) as $quiz ) {
        echo '<option value="' . esc_attr( $quiz->ID ) . '" ' . ( is_array( $selected_quizzes ) && in_array( $quiz->ID, $selected_quizzes ) ? 'selected' : '' ) . '>' . esc_html( $quiz->post_title ) . '</option>';
    }
    echo '</select><br><br>';

    // Icon Class
    echo '<p><strong>' . __( 'Icon Class:', 'learndash-custom-tab' ) . '</strong></p>';
    echo '<input type="text" name="custom_tab_icon_class" value="' . esc_attr( $icon_class ) . '" placeholder="e.g. ld-custom-tab-icon" />';
}

// Save Meta Box Data
function custom_tab_save_meta_box_data( $post_id ) {
    if ( ! isset( $_POST['custom_tab_meta_box_nonce'] ) ) {
        return;
    }

    if ( ! wp_verify_nonce( $_POST['custom_tab_meta_box_nonce'], 'custom_tab_save_meta_box_data' ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    // Save user selection
    $selected_users = isset( $_POST['custom_tab_users'] ) ? array_map( 'sanitize_text_field', $_POST['custom_tab_users'] ) : [];
    update_post_meta( $post_id, '_custom_tab_users', $selected_users );

    // Save display on selection
    $display_on = isset( $_POST['custom_tab_display_on'] ) ? sanitize_text_field( $_POST['custom_tab_display_on'] ) : '';
    update_post_meta( $post_id, '_custom_tab_display_on', $display_on );

    // Save courses selection
    $selected_courses = isset( $_POST['custom_tab_courses'] ) ? array_map( 'sanitize_text_field', $_POST['custom_tab_courses'] ) : [];
    update_post_meta( $post_id, '_custom_tab_courses', $selected_courses );

    // Save lessons selection
    $selected_lessons = isset( $_POST['custom_tab_lessons'] ) ? array_map( 'sanitize_text_field', $_POST['custom_tab_lessons'] ) : [];
    update_post_meta( $post_id, '_custom_tab_lessons', $selected_lessons );

    // Save topics selection
    $selected_topics = isset( $_POST['custom_tab_topics'] ) ? array_map( 'sanitize_text_field', $_POST['custom_tab_topics'] ) : [];
    update_post_meta( $post_id, '_custom_tab_topics', $selected_topics );

    // Save quizzes selection
    $selected_quizzes = isset( $_POST['custom_tab_quizzes'] ) ? array_map( 'sanitize_text_field', $_POST['custom_tab_quizzes'] ) : [];
    update_post_meta( $post_id, '_custom_tab_quizzes', $selected_quizzes );

    // Save icon class
    if ( isset( $_POST['custom_tab_icon_class'] ) ) {
        $icon_class = sanitize_text_field( $_POST['custom_tab_icon_class'] );
        update_post_meta( $post_id, '_custom_tab_icon_class', $icon_class );
    }
}
add_action( 'save_post', 'custom_tab_save_meta_box_data' );

// Add Custom Tab to LearnDash
add_filter( 'learndash_content_tabs', function( $tabs = array(), $context = '', $course_id = 0, $user_id = 0 ) {
    $custom_tabs = get_posts( array( 'post_type' => 'custom_tab', 'numberposts' => -1 ) );

    foreach ( $custom_tabs as $custom_tab ) {
        $selected_users = get_post_meta( $custom_tab->ID, '_custom_tab_users', true );
        $display_on = get_post_meta( $custom_tab->ID, '_custom_tab_display_on', true );
        $selected_courses = get_post_meta( $custom_tab->ID, '_custom_tab_courses', true );
        $selected_lessons = get_post_meta( $custom_tab->ID, '_custom_tab_lessons', true );
        $selected_topics = get_post_meta( $custom_tab->ID, '_custom_tab_topics', true );
        $selected_quizzes = get_post_meta( $custom_tab->ID, '_custom_tab_quizzes', true );

        $show_tab = false;

        if ( $selected_users == 'all' || array_intersect( (array) $selected_users, [$user_id] ) ) {
            switch ( $display_on ) {
                case 'courses':
                    if ( $selected_courses == 'all' || array_intersect( (array) $selected_courses, [$course_id] ) ) {
                        $show_tab = true;
                    }
                    break;
                case 'lessons':
                    if ( $selected_lessons == 'all' || ( 'sfwd-lessons' === get_post_type() && in_array( get_the_ID(), (array) $selected_lessons ) ) ) {
                        $show_tab = true;
                    }
                    break;
                case 'topics':
                    if ( $selected_topics == 'all' || ( 'sfwd-topic' === get_post_type() && in_array( get_the_ID(), (array) $selected_topics ) ) ) {
                        $show_tab = true;
                    }
                    break;
                case 'quizzes':
                    if ( $selected_quizzes == 'all' || ( 'sfwd-quiz' === get_post_type() && in_array( get_the_ID(), (array) $selected_quizzes ) ) ) {
                        $show_tab = true;
                    }
                    break;
            }
        }

        if ( $show_tab ) {
            $tabs[ 'custom_tab_' . $custom_tab->ID ] = array(
                'id'      => 'custom_tab_' . $custom_tab->ID,
                'icon'    => esc_attr( get_post_meta( $custom_tab->ID, '_custom_tab_icon_class', true ) ),
                'label'   => esc_html( $custom_tab->post_title ),
                'content' => '<p>' . wp_kses_post( $custom_tab->post_content ) . '</p>',
            );
        }
    }

    return $tabs;
}, 30, 4 );

function learndash_custom_tab_enqueue_scripts() {
    // Enqueue Select2 CSS
    wp_enqueue_style( 'select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css' );
    wp_enqueue_style( 'learndash-custom-tab-style', plugins_url( '/assets/css/custom-tab-style.css', __FILE__ ) );

    // Enqueue jQuery if it's not already included
    wp_enqueue_script( 'jquery' );

    // Enqueue Select2 JS
    wp_enqueue_script( 'select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array( 'jquery' ), null, true );

    // Enqueue your custom script
    wp_enqueue_script( 'learndash-custom-tab-script', plugins_url( '/assets/js/custom-tab-script.js', __FILE__ ), array( 'jquery', 'select2-js' ), null, true );
}
add_action( 'admin_enqueue_scripts', 'learndash_custom_tab_enqueue_scripts' );


// Load plugin textdomain for translations
function learndash_custom_tab_load_textdomain() {
    load_plugin_textdomain( 'learndash-custom-tab', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'learndash_custom_tab_load_textdomain' );
?>
