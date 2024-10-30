<div class=" tab" data-tab="login">
  <div class="BlockLogin">
    <form id="loginform" action="" method="post">
      <input type="hidden" name="<?= $controller_key;?>" value="<?= $type;?>" />
      <?php \MemberWunder\Controller\User\General::message( $status, $type, 'login_courses' ); ?>
      <div class="field">
        <input 
          id="user_key"
          <?= !is_null( $status ) ? 'aria-describedby="login_'.( $status['status'] == FALSE ? 'error' : 'success' ).'"' : '';?>
          type="text" 
          name="user_key" 
          value="<?= $values['user_key']; ?>" 
          placeholder="<?php esc_attr_e( 'Email or Username', TWM_TD ); ?>" 
        />
      </div>
      <div class="field">
        <input 
          id="user_password"
          <?= !is_null( $status ) ? 'aria-describedby="login_'.( $status['status'] == FALSE ? 'error' : 'success' ).'"' : '';?>
          type="password" 
          name="user_password"
          value="<?= $values['user_password']; ?>" 
          placeholder="<?php esc_attr_e( 'Password', TWM_TD ); ?>" 
        />
      </div>
      <div class="ksv-xs-m15b">
        <button type="submit" name="wp-submit" id="wp-submit" class="but green w100">
          <?php esc_html_e( 'Log in', TWM_TD ); ?>
        </button>
      </div>
    </form>
    <div class="row">
      <div class="col-lg-8">
        <a class="hidden-md hidden-sm hidden-xs" href="<?= esc_url( twmshp_get_lostpassword_url() ); ?>" data-res="recover">
          <?php _e( 'Forgot your password?', TWM_TD ); ?>
        </a>
        <span class="visible-md visible-sm visible-xs">
          <a class="d-inline" href="<?= esc_url( twmshp_get_lostpassword_url() ); ?>" data-res="recover">
            <?php _e( 'Forgot your password?', TWM_TD ); ?>
          </a>
        </span> 
      </div>
      <?php if( twmshp_users_can_register() ): ?>
        <div class="col-lg-4">
          <div class="text-right hidden-md hidden-sm hidden-xs">
            <a href="<?= esc_url( twmshp_get_register_url() ); ?>" >
              <?php _e( 'Register', TWM_TD ); ?>
            </a>    
          </div>
          <div class="visible-md visible-sm visible-xs">
            <a class="d-inline" href="<?= esc_url( twmshp_get_register_url() ); ?>" >
              <?php _e( 'Register', TWM_TD ); ?>
            </a>    
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>