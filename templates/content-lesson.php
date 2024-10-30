<?php include __DIR__ . '/layout/header.php'; ?>

<?php
/** @var \WP_Post $course */
/** @var \WP_Post $module */
/** @var array $modules */
/** @var array $lessons */
/** @var \WP_Post|null $lesson_prev */
/** @var \WP_Post|null $lesson_next */
?>

<?php
$mapperLesson = \MemberWunder\Mapper\Lesson::getInstance();

$lessons_count_done = $mapperLesson->countDoneLessonsByCourse();
$lessons_count = $mapperLesson->countLessonsByCourse(getFreeStartedCoursesIds());
$lesson_available = $mapperLesson->isAvailable( get_the_ID(), $module );

$courseLessonsCount = isset($lessons_count[$course->ID]) ? $lessons_count[$course->ID] : 0;
$courseLessonsCountDone = isset($lessons_count_done[$course->ID]) ? $lessons_count_done[$course->ID] : 0;

if ($courseLessonsCount) {
    $val = $courseLessonsCountDone . '/' . $courseLessonsCount;
    $width = min(max(floor($courseLessonsCountDone * 100 / $courseLessonsCount), 1), 100);
} else {
    $val = '0/0';
    $width = '1';
}
?>


<div class="TopImgBlock">
    <?php $module_thumbnail_url = twmshp_get_image_url(get_post_meta($module->ID, 'image', true)); ?>
    <div class="bg" style="background-image: url('<?php echo esc_attr($module_thumbnail_url); ?>')"></div>
    <div class="container">
        <div class="row">
            <div class="col-sm-8">
                <div class="desc"><?php echo get_the_title($module); ?></div>
                <h1 class="fntB ksv-xs-m15"><?php the_title(); ?></h1>
                <a href="<?php echo esc_url(get_permalink($course)); ?>"
                   class="but trans">
                    <i class="icon ion-chevron-left"></i> <?php esc_html_e('Back to modules', TWM_TD); ?>
                </a>
            </div>
            <div class="col-sm-4">

            </div>
        </div>
    </div>
</div>
<div class="ksv-xs-h15"></div>
<div class="container">
    <div class="row">
        <div class="col-sm-8">
            <?php include 'content-lesson-'.( $lesson_available ? 'available' : ( $mapperLesson->is_module_blocked( $module, $course ) ? 'locked' : 'not-available' ) ).'.php'; ?>
        </div>
        <div class="col-sm-4">
            <div class="ksv-sm-m-75t">
                <div class="blockShadow blockNavCources">
                    
                    <div class="arrows">
                        <div class="col">
                            <?php if ($lesson_prev) { ?><a class="prev" href="<?php echo esc_url($lesson_prev); ?>"><i class="ion-chevron-left" aria-hidden="true"></i> <?php esc_html_e('Previous'); ?></a><?php } ?>
                        </div>
                        <div class="col text-center">
                            <div class="status"<?php if (!isset($doneLessonIds[get_the_ID()])) { ?> style="visibility: hidden;"<?php } ?>><i class="ion-checkmark-circled"></i> <?php esc_html_e('DONE'); ?></div>
                        </div>
                        <div class="col">
                            <?php if ($lesson_next) { ?><a class="next" href="<?php echo esc_url($lesson_next); ?>"><?php esc_html_e('Next'); ?> <i class="ion-chevron-right" aria-hidden="true"></i></a><?php } ?>
                        </div>
                    </div>
                    
                    <div class="modules">
                        <h3><?php esc_html_e('LESSONS', TWM_TD); ?></h3>
                        <?php
                            $n = 0;
                            /** @var \WP_Post $lesson */
                            foreach ($lessons as $lesson) 
                            {
                                $n++;
                                $quiz = get_post_meta($lesson->ID, 'quiz', true);
                                $quizTitle = empty($quiz['title']) ? '' : (string) $quiz['title'];
                                $isAvailable = $mapperLesson->isAvailable( $lesson->ID, $module );
                                if( twmshp_get_option( 'hide_unavailable_modules' ) && !$isAvailable )
                                    continue;
                        ?>
                                <a 
                                    class="item<?php if (get_the_ID() == $lesson->ID) { ?> active<?php } ?><?php if (isset($doneLessonIds[$lesson->ID])) { ?> done<?php } ?><?= !$isAvailable ? ' item-lesson-not-available' : '';?>"
                                    href="<?php echo esc_url(get_permalink($lesson)); ?>">
                                    <div class="stat"></div>
                                    <?php echo $n; ?>. <?php echo get_the_title($lesson); ?>
                                </a>
                                <?php if ($quiz) { ?>
                                    <a class="item" href="<?php echo esc_url(twmshp_get_lesson_quiz_url($lesson)); ?>">
                                        <div class="stat"></div>
                                        - <?php echo esc_html($quizTitle); ?>
                                    </a>
                                <?php } ?>
                                <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
            <?php if ( !isset($doneLessonIds[get_the_ID()]) && $lesson_available ) { ?>
                <div class="ksv-xs-m15 FileButton">
                    <a href="<?php echo wp_nonce_url(get_permalink(), 'done_' . get_the_ID(), 'action_nonce'); ?>" class="but green w100"><?php esc_html_e('Lesson done', TWM_TD); ?></a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
