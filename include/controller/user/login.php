<?php
  namespace MemberWunder\Controller\User;

  class Login extends General
  {
    /**
     * type of form
     * 
     * @var string
     *
     * @since 1.0.21.5
     * 
     */
    protected static $type = 'login';

    /**
     * values of form fields
     * 
     * @var array
     *
     * @since  1.0.21.5
     * 
     */
    protected static $values = array(  
                                    'user_key'          =>  '', 
                                    'user_password'     =>  ''
                                    );

    /**
     * validate user data
     *
     * @since  1.0.21.5
     * 
     */
    protected static function validate()
    {
        global $current_user;
        wp_get_current_user();

        if( is_user_logged_in() )
            return self::set_status( FALSE, __( 'You are already logged in.', TWM_TD ) );

        foreach( self::$values as $key => $value )
            if( empty( $value ) )
                return self::set_status( FALSE, __( 'Some field is empty.', TWM_TD ) ); 

        $user = get_user_by( is_email( self::$values[ 'user_key' ] ) ? 'email' : 'login', self::$values[ 'user_key' ] );

        if( !$user )
            return self::set_status( FALSE, __( 'There is no user registered.', TWM_TD ) );
            
        if( !wp_check_password( self::$values[ 'user_password' ] , $user->data->user_pass, $user->ID ) )
            return self::set_status( FALSE, __( 'The password you entered is incorrect.', TWM_TD ) );

        self::set_status( '', '' );
    }

    /**
     * handler form after validation
     * 
     * @since 1.0.21.5
     * 
     */
    protected static function handler()
    {
        $user = get_user_by( is_email( self::$values[ 'user_key' ] ) ? 'email' : 'login' , self::$values[ 'user_key' ] );

        wp_set_current_user( $user->ID, $user->data->user_login );
        wp_set_auth_cookie( $user->ID );
        do_action( 'wp_login', $user->data->user_login );
        
        self::set_status( TRUE, __( 'You are successfully logged in.', TWM_TD ) );
    }

    /**
     * actions after handler form
     * 
     * @since 1.0.26.12
     * 
     */
    protected static function after_handler()
    {
        /*
         * redirect to url from get parameter
         */
        if( !isset( $_REQUEST['mw-cr'] ) || empty( $_REQUEST['mw-cr'] ) )
            return;

        if( static::$status['status'] !== TRUE )
            return;

        $curs = (int)$_REQUEST['mw-cr'];

        if( empty( $curs ) )
            return;

        if( FALSE === get_post_status( $curs ) )
            return;
        
        wp_redirect( get_permalink( $curs ) );
        exit();
    }
  }