<?php include __DIR__ . '/layout/header-login.php'; ?>

<?php rewind_posts(); while (have_posts()) { the_post(); ?>

<?php
$error_codes = empty($_GET['error_codes']) ? array() : explode(',', (string)$_GET['error_codes']);
$user_login = isset($_GET['log']) ? (array_intersect(array('incorrect_password', 'empty_password'), $error_codes) ? wp_unslash((string)$_GET['log']) : '') : '';

$login_url = twmshp_get_dashboard_url();
?>

<div class="LoginForm content--editor">
    <?php $login_page_title = twmshp_get_option('login_page_title'); ?>
    <?php $login_page_subtitle = twmshp_get_option('login_page_subtitle'); ?>
    <?php $login_page_description = twmshp_get_option('login_page_description'); ?>

    <div class="LoginFormTitle ksv-xs-m30b">
    <?php if ($login_page_title) { ?>
        <h3 class="text-center ksv-xs-m0b"><?php echo esc_html($login_page_title); ?></h3>
    <?php } ?>
    <?php if ($login_page_subtitle) { ?>
        <h4 class="text-center ksv-xs-m0b"><?php echo esc_html($login_page_subtitle); ?></h4>
    <?php } ?>
    <?php if ($login_page_description) { ?>
        <p class="text-center ksv-xs-m15t"><?php echo $login_page_description; ?></p>
    <?php } ?>
    </div>

    <?php MemberWunder\Controller\User\General::render_form_by_type( 'login' );?>
</div>

<?php } ?>

<?php include __DIR__ . '/layout/footer-login.php'; ?>
