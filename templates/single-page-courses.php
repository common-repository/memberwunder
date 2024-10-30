<?php include __DIR__ . '/layout/header.php'; ?>

<?php
$mapperLesson = \MemberWunder\Mapper\Lesson::getInstance();
$my_courses_ids = getFreeStartedCoursesIds();

$all = isset($_GET['all']) ? true : false;
if ($all) {
    $courses = twmshp_get_courses();
} else {
    $courses = twmshp_get_courses( array( 'post__in' => !empty($my_courses_ids) ? $my_courses_ids : array( 0 ) ) );
    if (!$courses) 
    {
        $all = 1;
        $courses = twmshp_get_courses();
    }
}

$lessons_count_done = $mapperLesson->countDoneLessonsByCourse();
$lessons_count = $mapperLesson->countLessonsByCourse( $my_courses_ids );
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">

            <div class="CourceTitle">
                <h3><?php esc_html_e('COURSES', TWM_TD); ?></h3>
            
                <div class="ksv-xs-m30b">
                    <a href="<?php echo esc_url(twmshp_get_courses_url()); ?>" class="btn-shadow padh2<?php if ($all) { ?> disabled<?php } ?>"><?php esc_html_e('My courses', TWM_TD); ?></a>
                    <a href="<?php echo esc_url(add_query_arg(array('all' => 1), twmshp_get_courses_url())); ?>" class="btn-shadow padh2<?php if (!$all) { ?> disabled<?php } ?>"><?php esc_html_e('All courses', TWM_TD); ?></a>
                </div>
            </div>
            <div class="row CourceList">
                <?php if ($courses) { ?>
                    <?php foreach ($courses as $course) { ?>
                        <?php
                        if (!$all) {
                            $courseLessonsCount = isset($lessons_count[$course->ID]) ? $lessons_count[$course->ID] : 0;
                            $courseLessonsCountDone = isset($lessons_count_done[$course->ID]) ? $lessons_count_done[$course->ID] : 0;
                        }
                        $course_price_formatted = twmshp_get_course_price_formatted($course, 'all', $my_courses_ids);
                        ?>

                        <div class="col-xs-6 col-sm-4 col-md-3 col-lg-3 item <?php if (!$all && $courseLessonsCount === $courseLessonsCountDone) { ?>done<?php } ?> ksv-xs-m30b">
                            <a href="<?php the_permalink($course); ?>" class="cntr">
                                <?php $thumbnail_url = twmshp_get_image_url(get_the_post_thumbnail_url($course, 'twm_block_small')); ?>

                                <div class="img"<?php if ($thumbnail_url) { ?> style="background-image: url(<?php echo esc_attr($thumbnail_url); ?>)"<?php } ?>></div>
                                <div class="info">
                                    <?= ( $course_price_formatted ? '<div class="price">'.$course_price_formatted.'</div>' : '' ); ?>
                                    <div class="name"><?php echo get_the_title($course); ?></div>
                                </div>
                            </a>
                            <?php if (!$all) { ?>
                                <div class="status">
                                    <div class="status__container">
                                        <?php
                                        if ($courseLessonsCount) {
                                            $val = $courseLessonsCountDone . '/' . $courseLessonsCount;
                                            $width = min(max(floor($courseLessonsCountDone * 100 / $courseLessonsCount), 1), 100) . '%';
                                        } else {
                                            $val = '0/0';
                                            $width = '1%';
                                        }
                                        ?>
                                        <div class="bar" style="width: <?php echo $width; ?>;"><?php echo $val; ?></div>
                                        <div class="val"><?php echo $val; ?></div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>

    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
