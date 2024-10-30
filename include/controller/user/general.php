<?php
  namespace MemberWunder\Controller\User;

  class General
  {
    /**
     * type of form
     * 
     * @var string
     *
     * @since 1.0.21.5
     * 
     */
    protected static $type = '';

    /**
     * user controller key
     * 
     * @var string
     *
     * @since  1.0.21.5
     * 
     */
    public static $controller_key = 'mw-user-controller';

    /**
     * values of form fields
     * 
     * @var array
     *
     * @since  1.0.21.5
     * 
     */
    protected static $values = array();

    /**
     * status of form
     *     NULL     form not submitted
     *     FALSE    eror with form (not processed or not valid)
     *     TRUE     form submitted
     * 
     * @var null || array( 'status' => '', 'message' => '' )
     * 
     */
    public static $status = NULL;

    /**
     * list avaible actions
     * 
     * @var array
     *
     * @since  1.0.21.5
     * 
     */
    public static $actions = array();

    /**
     * load form data
     * 
     * @since 1.0.21.5
     * @since 1.0.26.12 added after_handler and before_load_data actions
     * 
     */
    protected static function load()
    {
        static::before_load_data();

        if( !isset( $_REQUEST[ self::$controller_key ] ) )
            return;

        $key = htmlspecialchars( $_REQUEST[ self::$controller_key ] );
        
        if( !in_array( $key, array_keys( self::$actions ) ) )
            return;

        if( $key != static::$type )
            return;
        
        foreach(  array_keys( static::$values ) as $field )
            if( isset( $_REQUEST[ $field ] ) )
                static::$values[ $field ] = htmlspecialchars( $_REQUEST[ $field ] );
            
        static::validate();

        if( static::$status['status'] !== FALSE )
            static::handler();

        static::after_handler();
    }

    /**
     * set status
     * 
     * @param boolean $status 
     * @param string $message
     *
     * @since  1.0.21.5
     * 
     */
    protected static function set_status( $status, $message )
    {
        static::$status = array( 'status' => $status, 'message' => $message );
    }

    /**
     * validate user data
     *
     * @since  1.0.21.5
     * 
     */
    protected static function validate(){}

    /**
     * handler form after validation
     * 
     * @since 1.0.21.5
     * 
     */
    protected static function handler(){}

    /**
     * actions before load data
     * 
     * @since 1.0.26.12
     * 
     */
    protected static function before_load_data(){}

    /**
     * actions after handler form
     * 
     * @since 1.0.26.12
     * 
     */
    protected static function after_handler(){}

    /**
     * actions before form render
     * 
     * @since 1.0.26.12
     * 
     */
    protected static function before_form_render(){}

    /**
     * actions after form render
     * 
     * @since 1.0.26.12
     * 
     */
    protected static function after_form_render(){}

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
                                                    'blocks/user/'.static::$type, 
                                                    array( 
                                                        'status'            => static::$status, 
                                                        'controller_key'    => self::$controller_key, 
                                                        'type'              => static::$type, 
                                                        'values'            => static::$values, 
                                                        ) 
                                                    );

        static::after_form_render();
    }

    /**
     * render form by type
     * 
     * @param string $type
     * 
     * @since 1.0.21.5
     * 
     */
    public static function render_form_by_type( $type )
    {
        if( !isset( self::$actions[ $type ] ) )
            return;

        call_user_func( array( self::$actions[ $type ], 'render' ) );
    }

    /**
     * init user controllers
     * 
     * @since 1.0.21.5
     * 
     */
    public static function init()
    {
        \MemberWunder\Helpers\General::load( 'controller/user/login', '\MemberWunder\Controller\User\Login', 'init_controller' );
        \MemberWunder\Helpers\General::load( 'controller/user/login_courses', '\MemberWunder\Controller\User\LoginCourses', 'init_controller' );
        \MemberWunder\Helpers\General::load( 'controller/user/lostpassword', '\MemberWunder\Controller\User\Lostpassword', 'init_controller' );
        \MemberWunder\Helpers\General::load( 'controller/user/lostpassword_courses', '\MemberWunder\Controller\User\LostpasswordCourses', 'init_controller' );
        \MemberWunder\Helpers\General::load( 'controller/user/profile', '\MemberWunder\Controller\User\Profile', 'init_controller' );
        \MemberWunder\Helpers\General::load( 'controller/user/profile_system', '\MemberWunder\Controller\User\ProfileSystem', 'init_controller' );
    
        self::load();
    }

    /**
     * init specific user controller
     * 
     * @since 1.0.21.5
     * 
     */
    public static function init_controller()
    {
        self::$actions[ static::$type ] = static::who();
        static::load();
    }

    /**
     * get child class
     * 
     * @return string
     *
     * @since  1.0.23.2
     * 
     */
    protected static function who()
    {
        return get_called_class();
    }

    /**
     * show validation message
     *
     * @param array $status
     * @param string $key
     * @param string $area
     * 
     * @since 1.0.29
     * 
     */
    public static function message( $status, $key = '', $area = '' )
    {
        $show = !empty( $area ) && !empty( $key ) && $key != $area ? FALSE : TRUE;

        if( $show && !is_null( $status ) )
            \MemberWunder\Helpers\Template\General::show_validation_message( $status['status'], $status['message'] );
    }
}