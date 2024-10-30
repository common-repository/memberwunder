<?php \MemberWunder\Controller\User\General::message( $status, $type, 'lostpassword' ); ?>
<form id="lostpasswordform" action="" method="post">
    <input type="hidden" name="<?= $controller_key;?>" value="<?= $type;?>" />
    <div class="row field">
        <div class="col-xs-12 lbl">
            <label for="user_key">
                <?php _e( 'Email or Username', TWM_TD ); ?>
            </label>
        </div>
        <div class="col-xs-12">
            <input 
                id="user_key" 
                type="text" 
                name="user_key" 
                value="<?= $values['user_key']; ?>" 
            />
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 ksv-xs-tr">
            <button type="submit" name="wp-submit" id="wp-submit" class="but w100 blue">
                <?php _e( 'Get New Password', TWM_TD ); ?>
            </button>
        </div>
    </div>
</form>