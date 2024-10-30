<?php rewind_posts(); while (have_posts()) { the_post(); ?>

<?php
$course = twmshp_get_course_by_post(get_the_ID());
$module = twmshp_get_module_by_post(get_the_ID());

$quiz = get_post_meta(get_the_ID() ,'quiz', true);
$quizTitle = empty($quiz['title']) ? '' : (string)$quiz['title'];
$quizContent = empty($quiz['content']) ? '' : (string)$quiz['content'];
$quizQuestions = empty($quiz['questions']) ? array() : (array)$quiz['questions'];
$quizCertificate = empty($quiz['certificate']) ? '' : (string)$quiz['certificate'];
$quizRetake = empty($quiz['retake']) ? '' : (string)$quiz['retake'];

$mapperLesson = \MemberWunder\Mapper\Lesson::getInstance();

$taken_quiz = $mapperLesson->getTakenLessonQuiz(get_the_ID());
$canTake = !$taken_quiz || $mapperLesson->canTakeLessonQuiz(get_the_ID(), $taken_quiz->modified_at);

$retake = !$taken_quiz || (
    (isset($_GET['action_nonce']) && wp_verify_nonce($_GET['action_nonce'], 'retake_' . get_the_ID())) &&
    $canTake
);

if ($retake) {
    include __DIR__ . '/content-lesson-quiz.php';
} else {
    include __DIR__ . '/content-lesson-quiz-taken.php';
}
?>

<?php } ?>

