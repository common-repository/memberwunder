<?php
  namespace MemberWunder\Services;

  class Info
  {  
    private static $_instance = NULL;

    /**
     * subpath of url for get info data
     * 
     * @var string
     *
     * @since  1.0.34.2
     * 
     */
    protected static $subpath = '/info/%s?lang=%s';

    /**
     * name ajax action
     * 
     * @var string
     *
     * @since  1.0.34.2
     * 
     */
    protected static $action = 'memberwunder_info_block';

    /**
     * array of available pages
     * 
     * @var array
     *
     * @since  1.0.34.2
     * 
     */
    protected static $available_pages = array( 'plugins.php', 'index.php', 'options-general.php' );

    /**
     * nonce for ajax
     * 
     * @var string
     *
     * @since  1.0.34.2
     * 
     */
    protected static $nonce = 'twm_info';

    private function __clone() {}

    private function __construct()
    {
      if( empty( self::$subpath ) )
        return;

      /**
       * add wrapper for info block
       */
      add_action( 'admin_notices', function(){
        global $pagenow;

        if( !in_array( $pagenow, self::$available_pages ) && !\MemberWunder\Helpers\General::is_post_type( array( TWM_COURSE_TYPE ), $pagenow ) )
          return;

        if( !current_user_can( 'manage_options' ) )
          return;

        twm_get_template_part( 'info_block', array( 'args' => array( 'action' => self::$action, 'nonce' => wp_create_nonce( self::$nonce ) ) ) );
      });

      add_action( 'wp_ajax_'.self::$action, array( __CLASS__, 'loader' ) );
    }

    /**
     * loading data for info block
     * 
     * @since 1.0.34.2
     * 
     */
    public static function loader()
    {
      if( !isset( $_REQUEST[ 'nonce' ] ) || !wp_verify_nonce( $_REQUEST[ 'nonce' ], self::$nonce ) )
        die();

      if( empty( self::$subpath ) )
        die();
      
      $response = wp_remote_get( sprintf( \MW_Manager::$cdn_url.self::$subpath, twm_is_pro() ? 'pro' : 'free', get_locale() ) );
      
      if( is_wp_error( $response ) )
        die();
      
      if( $response['response']['code'] != 200 )
        die();

      $response = json_decode( $response['body'] );

      echo trim( $response->html );
      exit();
    }

    public static function _instance()
    {
      if ( NULL === self::$_instance)
        self::$_instance = new self();

      return self::$_instance;
    }
  }