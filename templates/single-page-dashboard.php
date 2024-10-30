<?php include __DIR__ . '/layout/header.php'; ?>

<?php
$mapperLesson = \MemberWunder\Mapper\Lesson::getInstance();
$my_courses_ids   =   getFreeStartedCoursesIds();

$lessons_finished = $mapperLesson->countDoneLessons();
$lessons_unfinished = twshp_count_free_lessons() - $lessons_finished;
?>

<div class="TopImgBlock">
    <?php $dashboard_background_image = twmshp_get_image_url(twmshp_get_option('dashboard_background_image')); ?>
    <div class="bg"<?php if ($dashboard_background_image) { ?> style="background-image: url('<?php echo esc_url($dashboard_background_image); ?>');"<?php } ?>></div>
    <div class="container">
        <div class="row">
            <div class="col-sm-8 content--editor">
                <section>
                    <?php $dasboard_title = twmshp_get_option('dashboard_title'); ?>
                    <?php if ($dasboard_title) { ?>
                    <h1><?php echo esc_html($dasboard_title); ?></h1>
                    <?php } ?>
                    <?php $dashboard_subtitle = twmshp_get_option('dashboard_subtitle'); ?>
                    <?php if ($dashboard_subtitle) { ?>
                    <h2><?php echo esc_html($dashboard_subtitle); ?></h2>
                    <?php } ?>
                    <?php echo twmshp_get_option('dashboard_description'); ?>
                </section>
            </div>
            <div class="col-sm-4">
                <?php $user = wp_get_current_user(); ?>
                <?php if ($user->ID) { ?>
                    <div class="user-profile">
                        <div class="user-profile__container">
                            <div class="user-profile__avatar">
                                <div class="user-profile__avatar-img">
                                    <img class="img-responsive avatar-url" src="<?php echo esc_attr(twmshp_get_avatar_url($user->ID)); ?>">
                                    <a class="user-profile__avatar-add" href="" id="avatar_upload">
                                            <i class="icon ion-camera"></i>
                                    </a>
                                    <form id="avatar_upload_form" method="post" action="<?php echo twmshp_get_profile_url(); ?>" enctype="multipart/form-data" style="display: none;">
                                        <input type="hidden" name="action" value="twm_avatar_upload" />
                                        <input type="file" name="avatar" id="avatar_upload_file" multiple="false" />
                                        <?php wp_nonce_field('avatar_upload', 'avatar_nonce'); ?>
                                    </form>
                                </div>
                                <div class="user-profile__avatar-name">
                                    <span><?php echo esc_html($user->first_name . ' ' . $user->last_name); ?></span>
                                    <a class="user-profile__avatar-edit" href="<?php echo esc_url(twmshp_get_profile_url()); ?>">
                                            <i class="icon ion-edit"></i>
                                    </a>
                                    <!--<div class="last user-profile__avatar-note">Last visit 11 hour ago</div>-->
                                </div>
                            </div>
                            <div class="user-profile__lessons">
                                <div class="user-profile__lessons-item">
                                    <span><?php echo esc_html(str_pad($lessons_finished, 2, '0', STR_PAD_LEFT)); ?></span>
                                    <?php esc_html_e('Completed lessons', TWM_TD); ?>
                                </div>
                                <div class="user-profile__lessons-item">
                                    <span><?php echo esc_html(str_pad($lessons_unfinished, 2, '0', STR_PAD_LEFT)); ?></span>
                                    <?php esc_html_e('Unfinished lessons', TWM_TD); ?>
                                </div>
                            </div>
                        </div>
                        <div class="user-profile__buttons">
                            <a href="<?php echo esc_url(twmshp_get_courses_url()); ?>" class="but green w100 button button--prymary button--block"><?php esc_html_e('Browse courses', TWM_TD); ?></a>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php
$dashboard_notice_text = twmshp_get_option('dashboard_notice_text');
$dashboard_notice_image = twmshp_get_option('dashboard_notice_image');
$dashboard_notice_hash = $dashboard_notice_text ? md5($dashboard_notice_text) : null;
$viewed_notice_hash = empty($_COOKIE['twm_dashboard_notice_hash']) ? null : $_COOKIE['twm_dashboard_notice_hash'];
?>

<?php if ($dashboard_notice_text && $viewed_notice_hash !== $dashboard_notice_hash) { ?>
    <div class="Jakob collapse in content--editor" id="collapseJakob" data-hash="<?php echo esc_attr($dashboard_notice_hash); ?>">
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="cntr">
                        <section>
                            <?php if ($dashboard_notice_image) { ?>
                                <div class="foto">
                                    <img src="<?php echo esc_attr($dashboard_notice_image); ?>" />
                                </div>
                            <?php } ?>
                            <div class="text">
                                <?php echo $dashboard_notice_text; ?>
                            </div>
                            <a class="close" href="#collapseJakob" data-toggle="collapse" aria-expanded="false"></a>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<div class="ksv-xs-h15"></div>
<div class="container">
    <div class="row">
        <div class="col-sm-12">
            <?php 
                $my_courses         =   twmshp_get_courses(
                                            array(
                                                'post__in' => !empty($my_courses_ids) ? $my_courses_ids : array( 0 )
                                                )
                                        );

                if ( $my_courses ) 
                { 
                    $lessons_count_done = $mapperLesson->countDoneLessonsByCourse();
                    $lessons_count = $mapperLesson->countLessonsByCourse( $my_courses_ids );
            ?>
                <h3><?php _e('MY COURSES', TWM_TD); ?></h3>
                <div class="row CourceList">
                    <?php foreach ($my_courses as $course) { ?>
                        <?php
                        $course_thumbnail_url = twmshp_get_image_url(get_the_post_thumbnail_url($course, 'twm_block_small'));
                        $course_price_formatted = twmshp_get_course_price_formatted($course, 'all', $my_courses_ids);
                        $courseLessonsCount = isset($lessons_count[$course->ID]) ? $lessons_count[$course->ID] : 0;
                        $courseLessonsCountDone = isset($lessons_count_done[$course->ID]) ? $lessons_count_done[$course->ID] : 0;
                        ?>
                        <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 item <?php if ($courseLessonsCount === $courseLessonsCountDone) { ?>done<?php } ?> ksv-xs-m30b">
                            <a href="<?php the_permalink($course); ?>" class="cntr">
                                <div class="img"<?php if ($course_thumbnail_url) { ?> style="background-image: url(<?php echo esc_attr($course_thumbnail_url); ?>)"<?php } ?>></div>
                                <div class="info">
                                    <?= ( $course_price_formatted ? '<div class="price">'.$course_price_formatted.'</div>' : '' ); ?>
                                    <div class="name"><?php echo get_the_title($course); ?></div>
                                </div>
                            </a>
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
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

            <?php 
                $courses = $my_courses ? twmshp_get_courses( empty( $my_courses_ids ) ? array() : array( 'post__not_in' => $my_courses_ids ) ) : twmshp_get_courses();
                
                if( $courses ) 
                    { 
            ?>
            <h3><?php if ($my_courses) { _e('OTHER COURSES', TWM_TD); } else { _e('ALL COURSES', TWM_TD); } ?></h3>
            <div class="row CourceList">
                <?php foreach ($courses as $course) { ?>
                    <?php $course_thumbnail_url = twmshp_get_image_url(get_the_post_thumbnail_url($course, 'twm_block_small')); ?>
                    <?php $course_price_formatted = twmshp_get_course_price_formatted($course, 'all', $my_courses_ids); ?>
                    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 item ksv-xs-m30b">
                        <a href="<?php the_permalink($course); ?>" class="cntr">
                            <div class="img"<?php if ($course_thumbnail_url) { ?> style="background-image: url(<?php echo esc_attr($course_thumbnail_url); ?>)"<?php } ?>></div>
                            <div class="info">
                                <?= ( $course_price_formatted ? '<div class="price">'.$course_price_formatted.'</div>' : '' ); ?>
                                <div class="name"><?php echo get_the_title($course); ?></div>
                            </div>
                        </a>
                    </div>
                <?php } ?>
                </div>
                <!--
                            <div class="text-center">
                                <a href="" class="moreLink">Load more...</a>
                            </div>
                -->
<?php } ?>

        </div>
    </div>
</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
