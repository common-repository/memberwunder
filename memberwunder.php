<?php
  /**
  *  Plugin Name:     MemberWunder LMS - Learning Management System - Ein WordPress e-Learning Plugin
  *  Version:         1.0.2
  *  Plugin URI:      https://memberwunder.com
  *  Description:     Ein WordPress e-Learning (LMS) Plugin, um sogenannte WordPress Learning Management Systeme zu erstellen mit anpassbaren Designs und sofort einsetzbarem Front-End. (Kurse, Lektionen, Quizze und mehr)
  *  Author:          Jakob Hager
  *  Author URI:      https://memberwunder.com
  *  Text Domain:     tw-membership
  *  Domain Path:     /languages/
  **/

  add_action( 'plugins_loaded', array( 'MW_Manager', '_instance' ), 50 );
  register_activation_hook( __FILE__, array( 'MW_Manager', 'activate' ), 20 );

  class MW_Manager
  {
    private static $_instance = NULL;

    /**
     * version of php
     * 
     * @var string
     *
     * @since  1.0.0.0
     * 
     */
    protected static $php_environment = '5.3';

    /**
     * version of wp
     * 
     * @var string
     *
     * @since  1.0.0.0
     * 
     */
    protected static $wp_environment = '4.2.0';

    /**
     * URL for get data from MemberWunder CDN
     * 
     * @var string
     *
     * @since  1.0.34.2
     * 
     */
    public static $cdn_url = 'https://cdn.memberwunder.com';

    private function __construct()
    {
      self::set_constants();

      $flag = self::check_environment();
      if( !is_null( $flag ) )
        return;
       
      $ns = self::get_general_helper();
      
      $ns::load( 'functions' );

      $ns::load( 'helpers/view' );
      $ns::load( 'helpers/template/general' );
      $ns::load( 'helpers/import_export' );

      $ns::load( 'data_fields' );

      $ns::load( 'mapper/lesson' );

      $ns::load( 'controller/install' );
      $ns::load( 'controller/options/currency' );
      $ns::load( 'controller/options/colors' );
      $ns::load( 'controller/options/options' );
      $ns::load( 'controller/menu' );
      $ns::load( 'controller/pages' );
      $ns::load( 'controller/profile' );
      $ns::load( 'controller/register' );
      $ns::load( 'controller/reset' );

      $ns::load_by_action( 'controller/import_export/data', '\MemberWunder\Controller\ImportExport\Data', '_instance', 'init' );

      $ns::load( 'services/mail' );
      $ns::load( 'services/styles' );
      $ns::load( 'services/notice' );
      $ns::load( 'services/smtp' );

      $ns::load_by_action( 'services/info', '\MemberWunder\Services\Info', '_instance', 'init' );
      $ns::load_by_action( 'services/loader/courses', '\MemberWunder\Services\Loader\Courses', '_instance', 'init' );
      $ns::load_by_action( 'services/notice', '\MemberWunder\Services\Notice', 'handler_dismiss', 'wp_ajax_'.\MemberWunder\Services\Notice::$action );

      $ns::load( 'classes' );

      $ns::load( 'handlers/delete_course', '\MemberWunder\Handlers\DeleteCourse', '_instance' );
      add_action( 'plugins_loaded', 'twmshp_load_textdomain', 60 );

      $cl = 'MemberWunder\Application';
      $obj = new $cl;
      $obj->disableCache();
      add_action( 'plugins_loaded', array( $obj, 'run' ), 70 );
    }

    private function __clone() {}

    public static function _instance()
    {
      if ( NULL === self::$_instance)
        self::$_instance = new self();

      return self::$_instance;
    }

    /**
     * check is PRO version active?
     * 
     * @return boolean
     *
     * @since  1.0.0.0
     * 
     */
    public static function has_pro()
    {
      return in_array( 'tw-membership/tw-membership.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ? true : false;
    }

    /**
     * get information about plugin by type
     * 
     * @param  string $name Type of data field. 
     *                      Types https://codex.wordpress.org/File_Header
     * @return string       
     *
     * @since  1.0.0.0
     * 
     */
    public static function get_plugin_info( $name )
    {
      /** WordPress Plugin Administration API */
      require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

      $data = get_plugin_data( __FILE__ );
      
      if( $name == 'Release' )
      {
        $d = explode( '.', $data['Version'] );
        return $d[ sizeof($d) - 1 ];
      }

      return $data[$name];
    }

    /**
     * after activate plugin
     * 
     * @since 1.0.0.0
     * 
     */
    public static function activate()
    {
      if( self::has_pro() )
        wp_die( __( 'On your site uses plugin MemberWunder PRO. If you want activate MemberWunder FREE, please deactivate PRO version before.', TWM_TD ), __('Plugin Activation Error'), array('back_link' => true));

      self::set_constants();

      $flag = self::check_environment();

      if( !is_null( $flag ) )
      {
        deactivate_plugins(basename(__FILE__));
        $version = 'PHP' == $flag ? self::$php_environment : self::$wp_environment;

        $message = sprintf(__('<p>The <strong>MemberWunder</strong> plugin requires %s version %s or greater.</p>'), $flag, $version);
        
        wp_die($message, __('Plugin Activation Error'), array('back_link' => true));
      }

      $ns = self::get_general_helper();

      $ns::load( 'functions' );
      $ns::load( 'controller/install' );
      $ns::load( 'controller/options/options' );

      new \MemberWunder\Controller\Options();

      $ns::load( 'handlers/delete_course', '\MemberWunder\Controller\Install', '_instance' );
    }

    /**
     * get info about environment
     * 
     * @return string
     *
     * @since  1.0.0.0
     * 
     */
    protected static function check_environment()
    {
      global $wp_version;

      $flag = null;

      if ( version_compare( PHP_VERSION, self::$php_environment, '<' ) )
          $flag = 'PHP';
      elseif (version_compare($wp_version, self::$wp_environment, '<'))
          $flag = 'WordPress';

      return $flag;
    }

    /**
     * define constants
     * 
     * @param boolean $global
     *
     * @since 1.0.0
     * @since 1.0.19 dynamic update
     * @since 1.0.34.2 function set_constants
     * 
     */
    public static function set_constants( $global = true )
    {
      define( 'TWM_VERSION', MW_Manager::get_plugin_info('Version') );

      global $wpdb;

      define('TWM_TABLE_USER_LESSONS', $wpdb->base_prefix . 'twm_user_lessons');
      define('TWM_TABLE_USER_LESSONS_QUIZES', $wpdb->base_prefix . 'twm_user_lessons_quizes');

      define('TWM_COURSE_TYPE', 'twmembership');
      define('TWM_MODULE_TYPE', 'twmembership_modules');
      define('TWM_LESSONS_TYPE', 'twmembership_lessons');
      define('TWM_REST_API_URL', 'https://license.memberwunder.com/memberwunder/');

      define('TWM_TD', 'tw-membership');
      define('TWM_HOOK_PREFIX', 'memberwunder');
    
      define('TWM_FILE', __FILE__);
      define('TWM_PATH', dirname(TWM_FILE));
      define('TWM_BASENAME', plugin_basename(TWM_FILE));
      define('TWM_ASSETS_URL', plugins_url('/assets', __FILE__));
      define('TWM_TEMPLATES_URL', plugins_url('/templates', __FILE__));
    }

    /**
     * load general helper
     * 
     * @since 1.0.0.0
     * 
     */
    protected static function get_general_helper()
    {
      require_once( TWM_PATH . DIRECTORY_SEPARATOR . 'include'. DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR .'general.php' );
      return 'MemberWunder\Helpers\General';
    }
  }