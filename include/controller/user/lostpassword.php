<?php
  namespace MemberWunder\Controller\User;

  class Lostpassword extends General
  {
    /**
     * type of form
     * 
     * @var string
     *
     * @since 1.0.21.5
     * 
     */
    protected static $type = 'lostpassword';

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
        
        add_filter( 'allow_password_reset', array( __CLASS__, 'allow_password_reset_key' ), 99999, 2 );
            $answer = self::retrieve_password( $user );
        remove_filter( 'allow_password_reset', array( __CLASS__, 'allow_password_reset_key' ), 99999 );
        
        if( $answer ){
            self::set_status( TRUE, __( 'Check your email for the password restore instructions.', TWM_TD ) );
            
            /**
             * Reset value in field
             */
            self::$values['user_key'] = '';
        }else
            self::set_status( FALSE, __( 'The email could not be sent.', TWM_TD ) );
    }

    /**
     * allow password reset key
     * 
     * @param  string $value
     * @param  WP_User $user
     * 
     * @return boolean
     *
     * @since  1.0.21.5
     * 
     */
    public static function allow_password_reset_key( $value, $user )
    {
        return TRUE;
    }

    /**
     * Handles sending password retrieval email to user.
     *
     * @param WP_User $user_data
     * 
     * @return bool
     *
     * @since  1.0.21.5
     * 
     */
    private static function retrieve_password( $user_data ) 
    {
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;
        $key = get_password_reset_key( $user_data );
        
        if ( is_wp_error( $key ) )
            return $key;

        $message = __( 'Someone has requested a password reset for the following account:', TWM_TD ) . "\r\n\r\n";
        $message .= network_home_url( '/' ) . "\r\n\r\n";
        $message .= sprintf( __('Username: %s', TWM_TD ), $user_login) . "\r\n\r\n";
        $message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', TWM_TD ) . "\r\n\r\n";
        $message .= __( 'To reset your password, visit the following address:', TWM_TD ) . "\r\n\r\n";
        $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . ">\r\n";

        if( is_multisite() )
            $blogname = get_network()->site_name;
        else{
            /*
             * The blogname option is escaped with esc_html on the way into the database
             * in sanitize_option we want to reverse this for the plain text arena of emails.
             */
            $blogname = wp_specialchars_decode( get_option('blogname'), ENT_QUOTES );
        }

        $title = sprintf( __( '[%s] Password Reset', TWM_TD ), $blogname );

        /**
         * Filters the subject of the password reset email.
         *
         * @since 2.8.0
         * @since 4.4.0 Added the `$user_login` and `$user_data` parameters.
         *
         * @param string  $title      Default email title.
         * @param string  $user_login The username for the user.
         * @param WP_User $user_data  WP_User object.
         */
        $title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

        /**
         * Filters the message body of the password reset mail.
         *
         * If the filtered message is empty, the password reset email will not be sent.
         *
         * @since 2.8.0
         * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
         *
         * @param string  $message    Default mail message.
         * @param string  $key        The activation key.
         * @param string  $user_login The username for the user.
         * @param WP_User $user_data  WP_User object.
         */
        $message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );
        
        if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) )
            return FALSE;
            
        return TRUE;
    }
  }