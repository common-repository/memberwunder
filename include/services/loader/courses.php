<?php
  namespace MemberWunder\Services\Loader;

  class Courses
  {  
    private static $_instance = NULL;

    /**
     * subpath of url for get info data
     * 
     * @var string
     *
     * @since  1.0.36
     * 
     */
    protected static $subpath = '/data/';

    /**
     * action for load courses
     * 
     * @var string
     *
     * @since  1.0.36
     * 
     */
    protected static $action = 'memberwunder_courses_loader';

    /**
     * nonce for load courses
     * 
     * @var string
     *
     * @since  1.0.36
     * 
     */
    protected static $nonce  = 'twm_courses_loader';

    /**
     * list of available types
     * 
     * @var array
     *
     * @since  1.0.36
     * 
     */
    protected static $available = array( 'sample_course' );

    private function __clone() {}

    private function __construct()
    {
      if( empty( self::$subpath ) )
        return;

      add_action( 'wp_ajax_'.self::$action, array( __CLASS__, 'loader' ) );

      new \MemberWunder\Services\Notice( 
              self::$action.'_sample_course',
              array( 
                  'text'    =>  __( "Here you can upload our <strong>sample course</strong> to your membership area. Feel free to fully customize your membership area with our demo content. To install the demo content, click on the Button '<strong>Upload</strong>'.", TWM_TD ), 
                  'link'    =>  array( 
                                    'href' => \MemberWunder\Helpers\General::admin_ajax_link( 
                                                array( 
                                                  'action'  =>  self::$action, 
                                                  'nonce'   =>  wp_create_nonce( self::$nonce ),
                                                  'type'    =>  'sample_course',
                                                  ) 
                                              ), 
                                    'text' => __( 'Upload', TWM_TD ) 
                                    ),
                  'is_ajax' =>  TRUE,
                  'dismiss' =>  FALSE
                  ),
              array( 'mw_options' ) 
                );
    }

    /**
     * load data from CDN to site
     * 
     * @since 1.0.36
     * 
     */
    public static function loader()
    {
      if( !isset( $_REQUEST[ 'nonce' ] ) || !wp_verify_nonce( $_REQUEST[ 'nonce' ], self::$nonce ) )
        die();

      if( empty( self::$subpath ) )
        die();

      if( !isset( $_REQUEST['type'] ) || empty( $_REQUEST['type'] ) || !in_array( htmlspecialchars( $_REQUEST['type'] ), self::$available ) )
        \MemberWunder\Helpers\General::ajax_response( 'error', __( 'You have not selected the right action or your action is invalid.', TWM_TD ) );

      $function = htmlspecialchars( $_REQUEST['type'] ).'_loader';
      self::$function();
      
      \MemberWunder\Helpers\General::ajax_response( 'success', __( 'We have successfully loaded your data.', TWM_TD ) );
    }

    /**
     * handler load sample course
     * 
     * @since 1.0.36
     * 
     */
    protected static function sample_course_loader()
    {
      $temp_file = download_url( \MW_Manager::$cdn_url.self::$subpath.htmlspecialchars( $_REQUEST['type'] ).'.zip?lang='.get_locale() );
      
      if( is_wp_error( $temp_file ) )
        \MemberWunder\Controller\ImportExport\Import::set_response( 'error', sprintf( __( 'Error: %s', TWM_TD ), implode( ' ', $temp_file->get_error_messages() ) ) );

      try
      {
        \MemberWunder\Controller\ImportExport\Import::handler_file( $temp_file );
      }catch( \Exception $e ){
        \MemberWunder\Controller\ImportExport\Directory::level_down( true );
        \MemberWunder\Controller\ImportExport\Import::set_response( 'error', $e->getMessage() );
      }
      
      \MemberWunder\Controller\ImportExport\Directory::level_down( true );

      wp_delete_file( $temp_file );

      \MemberWunder\Services\Notice::add_to_dismissed( self::$action.'_'.htmlspecialchars( $_REQUEST['type'] ) );

      \MemberWunder\Controller\ImportExport\Import::set_response( 'success', __( 'You have successfully installed our sample course to your membership area. You can now fully customize it and exchange it with your content.', TWM_TD ) );
    }

    public static function _instance()
    {
      if ( NULL === self::$_instance)
        self::$_instance = new self();

      return self::$_instance;
    }
  }