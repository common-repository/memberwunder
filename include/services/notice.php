<?php
  namespace MemberWunder\Services;

  class Notice
  {  
    /**
     * data for loading in notice
     * 
     * @var array
     *
     * @since  1.0.36
     * 
     */
    protected $args = array();

    /**
     * pages for show admin notice
     * all        - all pages
     * mw         - only pages for MW
     * mw_options - only MW options page
     * dashboard  - dashboard page
     * 
     * @var array
     *
     * @since  1.0.36
     * 
     */
    protected $areas = array();

    /**
     * key of notice block
     * 
     * @var string
     *
     * @since  1.0.36
     * 
     */
    protected $key   = '';

    /**
     * action for dismiss notice
     * 
     * @var string
     *
     * @since  1.0.36
     * 
     */
    public static $action = 'memberwunder_dismiss_notice';

    /**
     * name of field for user meta
     * 
     * @var string
     *
     * @since  1.0.36
     * 
     */
    protected static $dismissed_field_key = 'memberwunder_dismissed_notices';

    /**
     * nonce for dismiss notice
     * 
     * @var string
     *
     * @since  1.0.36
     * 
     */
    protected static $nonce = 'twm_dismiss_notice';

    protected static $available_pages = array(
                                          'mw'          =>  array( 'plugins.php', 'index.php', 'options-general.php' ),
                                          'mw_options'  =>  array( 'options-general.php' ),
                                          'dashboard'   =>  array( 'index.php' ),
                                          'media'       =>  array( 'upload.php' )
                                            );

    public function __construct( $key, $args = array(), $areas = array() ) 
    {
      $this->args = $args;
      $this->areas = $areas;
      $this->key = $key;

      if( in_array( 'mw_options', $this->areas ) )
      {
        unset( $this->areas[ array_search( 'mw_options', $this->areas ) ] );
        $this->areas = array_merge( array( 'mw_options' ), $this->areas );
      }

      add_action( 'admin_notices', array( $this, 'notice' ) );
    }

    /**
     * render notice block
     * 
     * @since 1.0.36
     * 
     */
    public function notice()
    {
      if( !self::check_area( $this->areas, $this->key ) )
        return;

      twm_get_template_part( 'notice', array_merge( $this->args, array( 'dismiss_url' => \MemberWunder\Helpers\General::admin_ajax_link( array( 'action' => self::$action, 'nonce' => wp_create_nonce( self::$nonce ), 'key' => $this->key ) ) ) ) );
    }

    /**
     * dismiss block for user
     * 
     * @since 1.0.36
     * 
     */
    public static function handler_dismiss()
    {
      if( !isset( $_REQUEST[ 'nonce' ] ) || !wp_verify_nonce( $_REQUEST[ 'nonce' ], self::$nonce ) )
        die();

      if( !isset( $_REQUEST[ 'key' ] ) || empty( $_REQUEST[ 'key' ] ) )
        die();
      
      $user_id = get_current_user_id();

      if( !$user_id )
        die();

      echo self::add_to_dismissed( htmlspecialchars( $_REQUEST[ 'key' ] ), $user_id ) ? 'success' : 'error';
      exit();
    }

    /**
     * check area for showing notice block
     * 
     * @param  array $areas
     * @param  string $key
     * 
     * @return 1.0.36
     * 
     */
    public static function check_area( $areas, $key = NULL ) 
    {
      if( !current_user_can( 'manage_options' ) )
          return FALSE;

      global $pagenow;

      foreach( $areas as $area )
        switch ($area) 
        {
          case 'mw_options':
            if( in_array( $pagenow, self::$available_pages[ $area ] ) && isset( $_REQUEST['page'] ) && htmlspecialchars( $_REQUEST['page'] ) == \MemberWunder\Controller\Options::ADMIN_PAGE )
              return TRUE;
            break;
          case 'mw':
            if( in_array( $pagenow, self::$available_pages[ $area ] ) || \MemberWunder\Helpers\General::is_post_type( array( TWM_COURSE_TYPE ), $pagenow ) )
              return self::check_for_user( $key );
            break;
          case 'dashboard':
          case 'media':
            if( in_array( $pagenow, self::$available_pages[ $area ] ) )
              return self::check_for_user( $key );
            break;
          default:
            return self::check_for_user( $key );
        }

      return FALSE;
    }

    /**
     * check notice block for user
     * 
     * @param  string $key
     * 
     * @return boolean
     *
     * @since  1.0.36
     * 
     */
    protected static function check_for_user( $key )
    {
      $user_id = get_current_user_id();

      if( !is_null( $key ) && $user_id )
      {
        $dismissed = get_user_meta( $user_id, self::$dismissed_field_key, true );
        
        if( array_key_exists( $key, (array)$dismissed ) )
          return FALSE;
      }

      return TRUE;
    }

    /**
     * add block by key to dismissed for current user
     * 
     * @param string $key
     * @param int||boolean||NULL $user_id
     *
     * @return boolean
     * 
     * @since  1.0.36
     * 
     */
    public static function add_to_dismissed( $key, $user_id = NULL )
    {
      if( $user_id === NULL )
      {
        $user_id = get_current_user_id();
        if( !$user_id )
          return FALSE;
      }

      $dismissed = get_user_meta( $user_id, self::$dismissed_field_key, true );
      
      if( empty( $dismissed ) )
        $dismissed = array();

      $dismissed[ $key ] = current_time( 'mysql', 1 );

      return update_user_meta( $user_id, self::$dismissed_field_key, $dismissed );
    }
  }