jQuery(document).ready(function($) {
    // Show/hide users
    $('select[name="custom_tab_users_option"]').change(function() {
        $('#custom_tab_users').toggle($(this).val() === 'selected');
    });

    // Show/hide courses
    $('select[name="custom_tab_courses_option"]').change(function() {
        $('#custom_tab_courses').toggle($(this).val() === 'selected');
    });

    // Show/hide lessons
    $('select[name="custom_tab_lessons_option"]').change(function() {
        $('#custom_tab_lessons').toggle($(this).val() === 'selected');
    });

    // Show/hide topics
    $('select[name="custom_tab_topics_option"]').change(function() {
        $('#custom_tab_topics').toggle($(this).val() === 'selected');
    });

    // Show/hide quizzes
    $('select[name="custom_tab_quizzes_option"]').change(function() {
        $('#custom_tab_quizzes').toggle($(this).val() === 'selected');
    });

    jQuery(document).ready(function($) {
        $('.select2').select2();
    });
    
});
