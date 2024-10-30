<?php include __DIR__ . '/layout/header-register.php'; ?>

<?php rewind_posts(); while (have_posts()) { the_post(); ?>

<?php
$redirect_to = empty($_GET['redirect_to']) ? twmshp_get_dashboard_url() : (string)$_GET['redirect_to'];
?>

<div class="LoginForm ksv-sm-m60t ksv-md-m75t ksv-lg-m90t">
    <h3><?php _e('SIGN IN', TWM_TD); ?></h3>
    <?php
    $error = do_shortcode('[memberwunder-register-errors]');

    $user_name = empty($_GET['twm_user_name']) ? '' : $_GET['twm_user_name'];
    $user_email = empty($_GET['twm_user_email']) ? '' : $_GET['twm_user_email'];
    $first_name = empty($_GET['twm_first_name']) ? '' : $_GET['twm_first_name'];
    $last_name = empty($_GET['twm_last_name']) ? '' : $_GET['twm_last_name'];

    $aria_describedby_error = '';
    if ($error) {
        echo '<div id="login_error" class="login_error">' . $error . "</div><br />\n";
        $aria_describedby_error = ' aria-describedby="login_error"';
    }

    if (is_multisite()) {
        $registration_url = twmshp_get_register_url();
    } else {
        $registration_url = wp_registration_url();
    }

    if (empty($_GET['twm_finished'])) {
    ?>
    <form name="registerform" id="registerform" action="<?php echo esc_url($registration_url); ?>" method="post">
        <input type="hidden" name="memberwunder_register" value="1" />
        <?php if (is_multisite()) { ?>
            <input type="hidden" name="stage" value="validate-user-signup" />
            <?php
            /** This action is documented in wp-signup.php */
            do_action( 'signup_hidden_fields', 'validate-user' );
            ?>
            <input type="hidden" name="signup_for" value="user" />
            <div class="row field">
                <div class="col-xs-12 col-sm-5 lbl"><label for="user_name"><?php _e('Username', TWM_TD); ?></label></div>
                <div class="col-xs-12 col-sm-7"><input id="user_name"<?php echo $aria_describedby_error; ?> type="text" size="25" autocapitalize="none" autocorrect="off" maxlength="60" name="user_name" value="<?php echo esc_attr($user_name); ?>" /></div>
            </div>
            <div class="row field">
                <div class="col-xs-12 col-sm-5 lbl"><label for="user_email"><?php _e('Email', TWM_TD); ?></label></div>
                <div class="col-xs-12 col-sm-7"><input id="user_email"<?php echo $aria_describedby_error; ?> type="email" size="25" name="user_email" value="<?php echo esc_attr($user_email); ?>" /></div>
            </div>
        <?php } else { ?>
            <div class="row field">
                <div class="col-xs-12 col-sm-5 lbl"><label for="user_email"><?php _e('Email', TWM_TD); ?></label></div>
                <div class="col-xs-12 col-sm-7"><input id="user_email"<?php echo $aria_describedby_error; ?> type="email" size="25" name="user_email" value="<?php echo esc_attr($user_email); ?>" /></div>
            </div>
            <div class="row field">
                <div class="col-xs-12 col-sm-5 lbl"><label for="first_name"><?php _e('First name', TWM_TD); ?></label></div>
                <div class="col-xs-12 col-sm-7"><input id="first_name"<?php echo $aria_describedby_error; ?> type="text" size="25" name="first_name" value="<?php echo esc_attr($first_name); ?>" /></div>
            </div>
            <div class="row field">
                <div class="col-xs-12 col-sm-5 lbl"><label for="last_name"><?php _e('Last name', TWM_TD); ?></label></div>
                <div class="col-xs-12 col-sm-7"><input id="last_name"<?php echo $aria_describedby_error; ?> type="text" name="last_name" value="<?php echo esc_attr($last_name); ?>" /></div>
            </div>
            <div class="row field">
                <div class="col-xs-12 col-sm-5 lbl"><label for="user_pass"><?php _e('Password', TWM_TD); ?></label></div>
                <div class="col-xs-12 col-sm-7"><input id="user_pass"<?php echo $aria_describedby_error; ?> type="password" name="pwd" value="" /></div>
            </div>
            <div class="row field">
                <div class="col-xs-12 col-sm-5 lbl"><label for="confirm_pass"><?php _e('Confirm Password', TWM_TD); ?></label></div>
                <div class="col-xs-12 col-sm-7"><input id="confirm_pass"<?php echo $aria_describedby_error; ?> type="password" name="confirm_pwd" value="" /></div>
            </div>
            <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>" />
            <input type="hidden" name="testcookie" value="1" />
        <?php } ?>
        <div class="row">
            <div class="col-xs-12 col-sm-6 col-sm-offset-5 ksv-xs-tr">                
                <button type="submit" name="wp-submit" id="wp-submit" class="but blue w100"><?php _e('Sing up', TWM_TD); ?></button>
            </div>
        </div>        
    </form>
    <?php } else { ?>
    <p><?php printf(__('%s is your new username'), '<strong>' . $user_name . '</strong>'); ?></p>
    <p><?php _e('But, before you can start using your new username, <strong>you must activate it</strong>.'); ?></p>
    <p><?php printf(__('Check your inbox at %s and click the link given.'), '<strong>' . $user_email . '</strong>'); ?></p>
    <p><?php _e('If you do not activate your username within two days, you will have to sign up again.'); ?></p>
    <?php } ?>
</div>

<?php } ?>

<?php include __DIR__ . '/layout/footer-login.php'; ?>