<div class=" tab" data-tab="recover">
    <div class="BlockLogin">
        <form id="lostpasswordform" action="" method="post">
            <input type="hidden" name="<?= $controller_key;?>" value="<?= $type;?>" />
            <?php \MemberWunder\Controller\User\General::message( $status, $type, 'lostpassword_courses' ); ?>
            <div class="field">
                <input 
                    id="user_key" 
                    type="text" 
                    name="user_key" 
                    value="<?= $values['user_key']; ?>" 
                    placeholder="<?php esc_attr_e( 'Email or Username', TWM_TD ); ?>" 
                />
            </div>
            <div class="ksv-xs-m15b">
                <button type="submit" name="wp-submit" id="wp-submit" class="but green w100">
                    <?php esc_html_e('Get New Password', TWM_TD); ?>
                </button>
            </div>
        </form>
        <a href="<?= esc_url( twmshp_get_dashboard_url() ); ?>" class="" data-res="login">
            <?php _e('Login'); ?>
        </a>
    </div>
</div>