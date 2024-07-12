<?php
/**
 * Plugin Name: LearnDash New Tab
 * Description: Adds a new tab next to the course and material tabs on the LearnDash course page.
 * Version: 1.0
 * Author: Abdul Moiz
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

// Hook into the learndash_content_tabs filter to add a new tab.
add_filter('learndash_content_tabs', 'add_custom_tab', 10, 2);

function add_custom_tab($tabs, $course_id) {
    // Add a new tab.
    $tabs['custom_tab'] = array(
        'id'      => 'custom_tab',
        'icon'    => 'dashicons-admin-site',
        'label'   => 'Custom Tab',
        'content' => get_custom_tab_content($course_id),
    );
    return $tabs;
}

function get_custom_tab_content($course_id) {
    // Generate the content dynamically based on the course ID.
    ob_start();
    ?>
    <div>
        <h2>Custom Tab Content</h2>
        <p>This is the custom content for course ID: <?php echo esc_html( $course_id ); ?></p>
       
    </div>
    <?php
    return ob_get_clean();
}
