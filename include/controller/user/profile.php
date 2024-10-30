<?php
  namespace MemberWunder\Controller\User;

  class Profile extends General
  {
    /**
     * type of form
     * 
     * @var string
     *
     * @since 1.0.26.12
     * 
     */
    protected static $type = 'profile';

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
                            __( 'MY PROFILE', TWM_TD )  =>  array(
                                        array(
                                            'label'     =>  __( 'First Name', TWM_TD ),
                                            'key'       =>  'user_fist_name',
                                            'type'      =>  'text',
                                            'wp_key'    =>  'first_name'
                                            ),
                                        array(
                                            'label'     =>  __( 'Last Name', TWM_TD ),
                                            'key'       =>  'user_last_name',
                                            'type'      =>  'text',
                                            'wp_key'    =>  'last_name'
                                            ),
                                        array(
                                            'label'     =>  __( 'Display Name', TWM_TD ),
                                            'key'       =>  'user_display_name',
                                            'type'      =>  'text',
                                            'wp_key'    =>  'display_name'
                                            ),
                                        array(
                                            'label'     =>  __( 'Website', TWM_TD ),
                                            'key'       =>  'user_website',
                                            'type'      =>  'text',
                                            'wp_key'    =>  'user_url'
                                            ),
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

        if( empty( self::$values['user_display_name'] ) )
            return self::set_status( FALSE, __( 'Display name cannot be empty.', TWM_TD ) );

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
                                                    'blocks/user/'.self::$type, 
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
        $user_id = wp_update_user( 
                            array( 
                                'ID'            =>  self::$current_user->ID,
                                'user_url'      =>  self::$values[ 'user_website' ],
                                'first_name'    =>  self::$values[ 'user_fist_name' ],
                                'last_name'     =>  self::$values[ 'user_last_name' ],
                                'display_name'  =>  self::$values[ 'user_display_name' ]
                                ) 
                            );

        
        if( is_wp_error( $user_id ) )
            self::set_status( TRUE, __( 'Something going wrong.', TWM_TD ) );
        else
            self::set_status( TRUE, __( 'You are successfully update profile.', TWM_TD ) );        
    
        self::$current_user = get_user_by( 'ID', self::$current_user->ID );
    }
  }