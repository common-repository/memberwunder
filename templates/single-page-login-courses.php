<?php include __DIR__ . '/layout/header.php'; ?>

<?php rewind_posts(); while (have_posts()) { the_post(); ?>

<?php
$error_codes = empty($_GET['error_codes']) ? array() : explode(',', (string) $_GET['error_codes']);
$user_login = isset($_GET['log']) ? (array_intersect(array('incorrect_password', 'empty_password'), $error_codes) ? wp_unslash((string) $_GET['log']) : '') : '';
$login_url = twmshp_get_dashboard_url();
?>

    <div class="TopImgBlock">
        <?php $login_page_background_image = twmshp_get_image_url(twmshp_get_option('login_page_background_image')); ?>
        <div class="bg"<?php if ($login_page_background_image) { ?> style="background-image: url('<?php echo esc_url($login_page_background_image); ?>')"<?php } ?>></div>
        <div class="container">
            <div class="row">
                <div class="col-sm-8 ksv-sm-p60 content--editor">
                    <section>
                        <?php $login_page_title = twmshp_get_option('login_page_title'); ?>
                        <?php $login_page_subtitle = twmshp_get_option('login_page_subtitle'); ?>
                        <?php $login_page_description = twmshp_get_option('login_page_description'); ?>
                        <?php if ($login_page_title) { ?>
                            <h1><?php echo esc_html($login_page_title); ?></h1>
                        <?php } ?>
                        <?php if ($login_page_subtitle) { ?>
                            <h2><?php echo esc_html($login_page_subtitle); ?></h2>
                        <?php } ?>
                        <?php if ($login_page_description) { ?>
                            <?php echo $login_page_description; ?>
                        <?php } ?>
                    </section>
                </div>
                <div class="col-sm-4 ksv-sm-p75 ksv-xs-p45<?php /* ksv-tabs */ ?>">
                    <?php MemberWunder\Controller\User\General::render_form_by_type( 'login_courses' );?>
                </div>
            </div>
        </div>
    </div>
    <div class="ksv-xs-h30"></div>
    <div class="container">
        <?php 
            $all_courses = twmshp_get_courses( twmshp_args_login_curses() );
        ?>
        <?php if ($all_courses) { ?>
            <div class="row CourceList">
                <?php foreach ($all_courses as $course) { ?>
                    <?php $course_thumbnail_url = twmshp_get_image_url(get_the_post_thumbnail_url($course, 'twm_block_small')); ?>
                    <?php $course_price_formatted = twmshp_get_course_price_formatted($course, 'all'); ?>
                    <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 item ksv-xs-m30b">
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
        <?php } ?>
    </div>

<?php } ?>

<?php include __DIR__ . '/layout/footer.php'; ?>
