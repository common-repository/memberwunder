<?php
  namespace MemberWunder\Controller\User;

  class ProfileSystem extends General
  {
    /**
     * type of form
     * 
     * @var string
     *
     * @since 1.0.26.12
     * 
     */
    protected static $type = 'profile_system';

    /**
     * values of form fields
     * 
     * @var array
     *
     * @since  1.0.26.12
     * 
     */
    protected static $values = array();

    /**
     * list of fields with labels and groups
     * 
     * @var array
     *
     * @since  1.0.26.12
     * 
     */
    protected static $fields  = array();

    /**
     * current user 
     * 
     * @var null
     *
     * @since  1.0.26.12
     * 
     */
    private static $current_user = NULL;

    /**
     * actions before load data
     * 
     * @since 1.0.26.12
     * 
     */
    protected static function before_load_data()
    {
        self::$fields = array(
                            __( 'Email and Password', TWM_TD ) =>  array(
                                        array(
                                            'label'     =>  __( 'Email', TWM_TD ),
                                            'key'       =>  'user_email',
                                            'type'      =>  'email',
                                            'wp_key'    =>  'user_email'
                                            ),
                                        array(
                                            'label'     =>  __( 'Old Password', TWM_TD ),
                                            'key'       =>  'user_old_password',
                                            'type'      =>  'password'
                                            ),
                                        array(
                                            'label'     =>  __( 'Password', TWM_TD ),
                                            'key'       =>  'user_password',
                                            'type'      =>  'password'
                                            ),
                                        array(
                                            'label'     =>  __( 'Confirm Password', TWM_TD ),
                                            'key'       =>  'user_confirm_password',
                                            'type'      =>  'password'
                                            )
                                                                ),
                            );

        foreach( self::$fields as $group )
            foreach( $group as $field )
                self::$values[ $field['key'] ] = '';
    }

    /**
     * validate user data
     *
     * @since  1.0.26.12
     * 
     */
    protected static function validate()
    {
        self::get_last_user_data();

        if( !is_user_logged_in() )
            return self::set_status( FALSE, __( 'You are not logged in.', TWM_TD ) );

        if( !is_email( self::$values['user_email'] ) )
            return self::set_status( FALSE, __( 'Invalid email.', TWM_TD ) );

        if( empty( self::$values[ 'user_old_password' ] ) || !wp_check_password( self::$values[ 'user_old_password' ], self::$current_user->user_pass, self::$current_user->ID ) )
            return self::set_status( FALSE, __( 'Invalid old password.', TWM_TD ) );

        if( self::$values[ 'user_password' ] != self::$values[ 'user_confirm_password' ] )
            return self::set_status( FALSE, __( 'New passwords do not match.', TWM_TD ) );

        self::set_status( '', '' );
    }

    /**
     * get last user data for current user
     * 
     * @since  1.0.26.12
     * 
     */
    private static function get_last_user_data()
    {
        if( self::$current_user === NULL )
        {
             global $current_user;
            self::$current_user = wp_get_current_user();
        }

        self::$current_user = get_user_by( 'ID', self::$current_user->ID );
    }

    /**
     * set default values
     * 
     * @since 1.0.26.12
     * 
     */
    protected static function before_form_render()
    {
        self::get_last_user_data();

        if( self::$status === TRUE )
            return; 

        self::$values = array();
        
        foreach( self::$fields as $group )
            foreach( $group as $field ):
                self::$values[ $field['key'] ] = '';

                if( isset( $field[ 'wp_key' ] ) )
                {
                    $key = $field[ 'wp_key' ];
                    self::$values[ $field[ 'key' ] ] = self::$current_user->$key;
                }
            endforeach;
    }

    /**
     * render user form
     * 
     * @since 1.0.21.5
     * 
     */
    protected static function render()
    {
        static::before_form_render();

        \MemberWunder\Helpers\View::get_template_part( 
                                                    'blocks/user/profile', 
                                                    array( 
                                                        'status'            => self::$status, 
                                                        'controller_key'    => self::$controller_key, 
                                                        'type'              => self::$type, 
                                                        'values'            => self::$values, 
                                                        'fields'            => self::$fields,
                                                        ) 
                                                    );

        static::after_form_render();
    }

    /**
     * handler form after validation
     * 
     * @since 1.0.26.12
     * 
     */
    protected static function handler()
    {
        $args = array( 
                        'ID'            =>  self::$current_user->ID,
                        'user_email'    =>  self::$values[ 'user_email' ]
                    );

        if( !empty( self::$values[ 'user_password' ] ) )
            $args['user_pass'] = self::$values[ 'user_password' ];

        $user_id = wp_update_user( $args );

        
        if( is_wp_error( $user_id ) )
            self::set_status( TRUE, __( 'Something going wrong.', TWM_TD ) );
        else
            self::set_status( TRUE, __( 'You are successfully update profile.', TWM_TD ) );        
    
        self::$current_user = get_user_by( 'ID', self::$current_user->ID );
    }
  }