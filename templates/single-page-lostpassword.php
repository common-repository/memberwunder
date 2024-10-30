<?php include __DIR__ . '/layout/header-login.php'; ?>

<?php rewind_posts(); while (have_posts()) { the_post(); ?>

<?php
    $error_codes = empty($_GET['error_codes']) ? array() : explode(',', (string)$_GET['error_codes']);
    $redirect_to = empty($_GET['redirect_to']) ? add_query_arg(array('checkemail' => 'confirm'), twmshp_get_dashboard_url()) : (string)$_GET['redirect_to'];
    $user_login = isset($_GET['user_login']) ? wp_unslash((string)$_GET['user_login']) : '';
?>

<div class="LoginForm ksv-sm-m60t ksv-md-m75t ksv-lg-m90t">
    <h3><?php _e('Lost Password', TWM_TD); ?></h3>
    <?php MemberWunder\Controller\User\General::render_form_by_type( 'lostpassword' );?>
</div>

<?php } ?>

<?php include __DIR__ . '/layout/footer-login.php'; ?>
