<?php
    namespace MemberWunder\Controller\ImportExport;

    class Import
    {
      private static $_instance = NULL;

      /**
       * nonce for upload zip archive
       * 
       * @var string
       *
       * @since  1.0.34
       * 
       */
      protected static $nonce                 =   'memberwunder-zip-import';

      /**
       * name of action for upload zip archive
       * 
       * @var string
       *
       * @since  1.0.34
       * 
       */
      protected static $action                =   'twm-import';

      /**
       * response message
       * 
       * @var array
       *
       * @since  1.0.34
       * 
       */
      protected static $response              =   array( 'status' => '', 'message' => '' );

      /**
       * name of input for loading file
       * 
       * @var string
       *
       * @since  1.0.34
       * 
       */
      public static $file_input_name          =   'import-zip';

      /**
       * count of lessons, modules and courses successfully imported in system
       * 
       * @var array
       *
       * @since  1.0.34
       * 
       */
      protected static $success_count         =   array( 'lesson' => 0, 'course' => 0, 'module' => 0 );

      private function __construct()
      {
        add_action( 'wp_ajax_'.self::$action, array( __CLASS__, 'handler' ) );
      }

      /**
       * handler of import request
       * 
       * @since 1.0.34
       * @since 1.0.36 added function handler_file
       * 
       */
      public static function handler()
      {
        if( !isset( $_REQUEST['nonce'] ) || !wp_verify_nonce( $_REQUEST['nonce'], self::$nonce ) )
          die();

        $data = isset( $_FILES ) ? $_FILES : array();
        $uploaded_file = wp_handle_upload( $data[ self::$file_input_name ], array( 'test_form' => false ) );

        if( $uploaded_file && ! isset( $uploaded_file['error'] ) ) 
        {
          if( $uploaded_file[ 'type' ] != 'application/zip' )
          {
            wp_delete_file( $uploaded_file['file'] );
            self::set_response( 'error', __( 'You are loading not ZIP archive with courses.', TWM_TD ) );
          }

          try
          {
            self::handler_file( $uploaded_file['file'] );
          }catch( \Exception $e ){
            Directory::level_down( true );
            self::set_response( 'error', $e->getMessage() );
          }
          
          Directory::level_down( true );
          
          self::set_response( 
                      'updated', 
                      sprintf( 
                            __( 'You have successfully imported %s, %s, %s!', TWM_TD ), 
                            sprintf( _n( '%s course', '%s courses', self::$success_count[ 'course' ], TWM_TD ), number_format_i18n( self::$success_count[ 'course' ] ) ), 
                            sprintf( _n( '%s module', '%s modules', self::$success_count[ 'module' ], TWM_TD ), number_format_i18n( self::$success_count[ 'module' ] ) ), 
                            sprintf( _n( '%s lesson', '%s lessons', self::$success_count[ 'lesson' ], TWM_TD ), number_format_i18n( self::$success_count[ 'lesson' ] ) ) 
                              ) 
                            );
        }else
          self::set_response( 'error', $uploaded_file['error'] );
        
        exit();
      }

      /**
       * handler file with ZIP archive
       * 
       * @param  string $file
       * 
       * @since  1.0.36
       * 
       */
      public static function handler_file( $file )
      {
        WP_Filesystem();

        $unzipfile = unzip_file( $file, \MemberWunder\Helpers\General::get_upload_path() );
        wp_delete_file( $file );
        if( !$unzipfile || is_wp_error( $unzipfile ) )
          throw new \Exception( __( 'There was an error unzipping the file with courses.', TWM_TD ) );

        Directory::_instance( Export::$base_folder_name, \MemberWunder\Helpers\General::get_upload_path() );
        $path = Directory::get_current();

        if( !file_exists( $path ) )
          throw new \Exception( __( 'There was an error on loading courses to site.', TWM_TD ) );
        
        self::import( $path );
      }

      /**
       * import data to WordPress
       *
       * @param  string $path
       * 
       * @since 1.0.34
       * 
       */
      protected static function import( $path ) 
      {
        $dir = opendir( $path );
        while( $element = readdir( $dir ) )
           if( \MemberWunder\Helpers\General::is_dir( $element, $path ) )
           {
              $xml = $path.$element.'/info.xml';
              if( !file_exists( $xml ) )
                continue;

              $xml = simplexml_load_file( $xml );

              if( !$xml )
                throw new \Exception( __( 'There was an error on loading courses to site.', TWM_TD ) );

              if( !isset( $xml->tw_membership->course ) )
                throw new \Exception( __( 'Invalid format of xml file with courses.', TWM_TD ) );

              $course = $xml->tw_membership->course;

              $course_args = Data::element_data_from_xml( $course, 'course' );
              $course_id = wp_insert_post( $course_args );

              if( is_wp_error( $course_id ) )
                throw new \Exception( __( 'Error create element', TWM_TD ) );
              
              self::insert_child_elements( $course, 'module', array( 'course_id' => $course_id ) );

              self::plus_success( 'course' );
              Directory::level_down( true );
           }
      }

      /**
       * create child element for course
       * 
       * @param  SimpleXMLElement $node 
       * @param  string           $type 
       * @param  array            $ids 
       * 
       * @since 1.0.34
       * 
       */
      protected static function insert_child_elements( $node, $type, $ids = array() )
      {
        $node = (array)$node;
        $node = (array)$node[ $type.'s' ];
        $node = (array)$node[ $type ];
        if( isset( $node['post_type'] ) )
          $node = array( $node );
        
        foreach( $node as $element )
        {
          $args = Data::element_data_from_xml( $element, $type );
          if( !isset( $args['meta_input'] ) )
            $args['meta_input'] = array();

          $args['meta_input'] = array_merge( $args['meta_input'], $ids );

          $element_id = wp_insert_post( $args );
          if( is_wp_error( $element_id ) )
            throw new \Exception( __( 'Error create element', TWM_TD ) );

          $ids[ $type.'_id' ] = $element_id;

          if( $type != 'lesson' )
            self::insert_child_elements( $element, 'lesson', $ids );
          
          if( isset( $ids[ $type.'_id' ] ) )
            unset( $ids[ $type.'_id' ] );

          self::plus_success( $type );
          Directory::level_down( true );
        }
      }

      /**
       * set reponse message
       * 
       * @param string  $type
       * @param string  $message
       *
       * @since  1.0.34
       * 
       */
      public static function set_response( $type, $message )
      {
        self::$response = array( 'status' => $type, 'message' => $message );

        Directory::_destroy();

        \MemberWunder\Helpers\General::ajax_response( $type, $message );
      }

      /**
       * generate link to loading zip archive
       * 
       * @return 1.0.28.10
       * 
       */
      public static function link_to_zip()
      {
        return \MemberWunder\Helpers\General::admin_ajax_link( array( 'action' => self::$action, 'nonce' => wp_create_nonce( self::$nonce ) ) );   
      }

      private function __clone() {}

      public static function _instance()
      {
        if ( NULL === self::$_instance)
          self::$_instance = new self();

        return self::$_instance;
      }

      /**
       * add + 1 to count of success import
       * 
       * @param  string $type
       * 
       * @since 1.0.34
       * 
       */
      protected static function plus_success( $type )
      {
        if( isset( self::$success_count[ $type ] ) )
          self::$success_count[ $type ] += 1; 
      }
    }