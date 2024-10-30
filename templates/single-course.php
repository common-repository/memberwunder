<?php include __DIR__ . '/layout/header.php'; ?>

<?php rewind_posts(); while (have_posts()) { the_post(); ?>

<?php
    $mapperLesson = \MemberWunder\Mapper\Lesson::getInstance();

    $info = twmshp_get_course_info(get_the_ID());
    $modules = twmshp_get_modules_by_course(get_the_ID());

    $course_description = empty($info['description']) ? '' : $info['description'];
    $course_subject = empty($info['subject']) ? '' : $info['subject'];
    $course_level = empty($info['level']) ? '' : $info['level'];
    $course_duration = empty($info['duration']) ? 0 : (int)$info['duration'];
    $course_price_formatted = twmshp_get_course_price_formatted(get_the_ID(), 'course', getFreeStartedCoursesIds());
    $course_thumbnail_url = twmshp_get_image_url(get_the_post_thumbnail_url(null, 'twm_block_small'));

    $course_modules_count = count($modules);
?>

<div class="TopImgBlock">
    <div class="bg"<?php if ($course_thumbnail_url) { ?> style="background-image: url(<?php echo esc_attr($course_thumbnail_url); ?>)"<?php } ?>></div>
    <div class="container">
        <div class="row">
            <div class="col-sm-8">
                <h1><?php the_title(); ?></h1>
                <?php echo apply_filters('the_content', $course_description); ?>
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
            <?php the_content(); ?>

            <?php if ($modules) { ?>
            
            <div class="CourceTitle">
                <h3><?php esc_html_e('MODULES', TWM_TD); ?></h3>
            </div>

            <div class="row CourceList">
                <?php 
                    foreach ($modules as $module) 
                    {
                        $isAvailable = twmshp_moduleIsAvailable( $module );

                        if( twmshp_get_option( 'hide_unavailable_modules' ) && !$isAvailable )
                            continue;
                ?>
                    <?php $module_thumbnail_url = twmshp_get_image_url(get_post_meta($module->ID, 'image', true)); ?>
                    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-4 item <?php if ( $mapperLesson->isModuleFinished( $module->ID ) ) { ?>done<?php } ?> ksv-xs-m30b <?= !$isAvailable ? 'locked' : '';?>">
                        <a href="<?php the_permalink($module); ?>" class="cntr">
                            <div class="img"<?php if ($module_thumbnail_url) { ?> style="background-image: url(<?php echo esc_attr($module_thumbnail_url); ?>)"<?php } ?>></div>
                            <div class="info">
                                <div class="name"><?php echo get_the_title($module); ?></div>
                            </div>
                            <!--<div class="videos">2</div>-->
                            <?= !$isAvailable ? '<div class="lock"><i class="ion-android-lock"></i></div>' : '';?>
                        </a>
                    </div>
                <?php 
                        unset( $isAvailable );
                    } 
                ?>
            </div>

            <?php } ?>
        </div>
        <div class="col-sm-4">
            <div class="ksv-sm-m-120t">
                <div class="blockShadow DescTable">
                    <table>
                        <?php if ($course_subject) { ?>
                        <tr>
                            <td><i class="icon fa fa-graduation-cap" aria-hidden="true"></i></td>
                            <th><?php esc_html_e('Subject', TWM_TD); ?>:</th>
                            <td><?php echo esc_html($course_subject); ?></td>
                        </tr>
                        <?php } ?>
                        <?php if ($course_modules_count) { ?>
                        <tr>
                            <td><i class="icon fa fa-clock-o" aria-hidden="true"></i></td>
                            <th><?php esc_html_e('Duration', TWM_TD); ?>:</th>
                            <td><?php printf(_n('%s module', '%s modules', $course_modules_count, TWM_TD), $course_modules_count); ?><?php if ($course_duration) { ?>, <?php printf(_n('%s hr', '%s hrs', $course_duration, TWM_TD), $course_duration); ?><?php } ?></td>
                        </tr>
                        <?php } ?>
                        <tr>
                            <td><i class="icon fa fa-tags" aria-hidden="true"></i></td>
                            <th><?php esc_html_e('Price', TWM_TD); ?>:</th>
                            <td><?= $course_price_formatted; ?></td>
                        </tr>
                        <?php if ($course_level) { ?>
                        <tr>
                            <td><i class="icon fa fa-level-up" aria-hidden="true"></i></td>
                            <th><?php esc_html_e('Level', TWM_TD); ?>:</th>
                            <td><?php echo esc_html($course_level); ?></td>
                        </tr>
                        <?php } ?>
                    </table>
                </div>
            </div>
            <!--
            <h3><?php _e('YOUR PROGRESS', TWM_TD); ?></h3>
            <div class="row ProgressIcons">
                <div class="col-xs-3 start">Start</div>
                <div class="col-xs-3 start done">Start</div>
                <div class="col-xs-3">1 module</div>
                <div class="col-xs-3 done">2 module</div>
                <div class="col-xs-3">3 module</div>
                <div class="col-xs-3">4 module</div>
                <div class="col-xs-3 finish">Finish</div>
                <div class="col-xs-3 finish done">Finish</div>
            </div>
            -->
        </div>
    </div>
</div>

<?php } ?>

<?php include __DIR__ . '/layout/footer.php'; ?>
