<?php include __DIR__ . '/layout/header-login.php'; ?>


<div class="LoginForm ksv-sm-m60t ksv-md-m75t ksv-lg-m90t">

    <h3><?php _e('Pick a New Password', TWM_TD); ?></h3>

    <?php $aria_describedby_error = ''; ?>
    <?php if (count($attributes['errors']) > 0) : ?>
        <?php $aria_describedby_error = ' aria-describedby="login_error"'; ?>
        <div id="login_error">
            <?php foreach ($attributes['errors'] as $error) : ?>
                <p>
                    <?php echo $error; ?><br />
                </p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>



    <form name="loginform" id="loginform" action="<?php echo site_url('wp-login.php?action=resetpass'); ?>" method="post">
        <div class="row field">
            <div class="col-xs-12">
                <label for="pass1"><?php _e('New password', TWM_TD) ?></label><br>
                <input type="password" name="pass1" id="pass1" class="input" size="20" value="" autocomplete="off" />
            </div>
        </div>
        <div class="row field">
            <div class="col-xs-12">
                <label for="pass2"><?php _e('Repeat new password', TWM_TD) ?></label><br>
                <input type="password" name="pass2" id="pass2" class="input" size="20" value="" autocomplete="off" />
            </div>            
        </div>
        <div class="row">
            <div class="col-xs-12 ksv-xs-tr">

                <input type="submit" name="submit" id="resetpass-button"
                       class="but blue" value="<?php _e('Reset Password', TWM_TD); ?>" />

            </div>
        </div>

        <input type="hidden" id="user_login" name="rp_login" value="<?php echo esc_attr($attributes['login']); ?>" autocomplete="off" />
        <input type="hidden" name="rp_key" value="<?php echo esc_attr($attributes['key']); ?>" />
    </form>
</div>


<?php include __DIR__ . '/layout/footer-login.php'; ?>
