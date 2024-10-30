<?php rewind_posts(); while (have_posts()) { the_post(); ?>

<?php
$course = twmshp_get_course_by_post(get_the_ID());
$module = twmshp_get_module_by_post(get_the_ID());
$modules = twmshp_get_modules_by_course($course);
$lessons = twmshp_get_lessons_by_module($module);

$mapperLesson = \MemberWunder\Mapper\Lesson::getInstance();

$lesson_next = $mapperLesson->nav_link( 'next', get_the_ID(), $module, $modules );
$lesson_prev = $mapperLesson->nav_link( 'prev', get_the_ID(), $module, $modules );

$mark_as_done = (isset($_GET['action_nonce']) && wp_verify_nonce($_GET['action_nonce'], 'done_' . get_the_ID()));
if ($mark_as_done) {
    $mark_as_done = $mapperLesson->markLessonAsDone(get_the_ID());
}

$doneLessonIds = array_flip($mapperLesson->getDoneLessonIds());

if ($mark_as_done) {
    include __DIR__ . '/content-lesson-done.php';
} else {
    include __DIR__ . '/content-lesson.php';
}
?>

<?php } ?>

