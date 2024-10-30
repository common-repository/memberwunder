<?php \MemberWunder\Controller\User\General::message( $status, $type, 'login' ); ?>
<form id="loginform" action="" method="post">
    <input type="hidden" name="<?= $controller_key;?>" value="<?= $type;?>" />
    <div class="row field">
        <div class="col-sm-12 lbl">
            <label for="user_key">
                <?php _e( 'Email or Username', TWM_TD ); ?>
            </label>
        </div>
        <div class="col-sm-12">
            <input 
                id="user_key" 
                type="text" 
                name="user_key" 
                value="<?= $values['user_key']; ?>"
                <?= !is_null( $status ) ? 'aria-describedby="login_'.( $status['status'] == FALSE ? 'error' : 'success' ).'"' : '';?> 
            />
        </div>
    </div>
    <div class="row field">
        <div class="col-sm-12 lbl">
            <label for="user_password">
                <?php _e( 'Password', TWM_TD ); ?>
            </label>
        </div>
        <div class="col-sm-12">
            <input 
                id="user_password"
                type="password" 
                name="user_password" 
                value="<?= $values['user_password']; ?>" 
                <?= !is_null( $status ) ? 'aria-describedby="login_'.( $status['status'] == FALSE ? 'error' : 'success' ).'"' : '';?>
            />
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 ksv-xs-tr">
            <div class="forget">
                <a href="<?= esc_url( twmshp_get_lostpassword_url() ); ?>">
                    <?php _e('Forgot your password?', TWM_TD); ?>
                </a>
            </div>
            <button type="submit" name="wp-submit" id="wp-submit" class="but blue">
                <?php _e('Log in', TWM_TD); ?>
            </button>
        </div>
    </div>
</form>