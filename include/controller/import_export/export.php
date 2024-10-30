<?php
    namespace MemberWunder\Controller\ImportExport;

    class Export
    {
      /**
       * name of export action
       * 
       * @var string
       *
       * @since  1.0.34
       * 
       */
      protected static $action = 'twm-export';

      /**
       * name of archive with courses
       * 
       * @var string
       *
       * @since  1.0.34
       * 
       */
      protected static $archive_name = 'memberwunder-courses.zip';

      /**
       * name of base folder
       * 
       * @var string
       *
       * @since  1.0.34
       * 
       */
      public static $base_folder_name = 'memberwunder_courses';

      /**
       * zip archive
       * 
       * @var null || ZipArchive
       *
       * @since  1.0.34
       * 
       */
      protected static $zip = NULL;

      private static $_instance = NULL;

      private function __construct()
      {
        //  add bulk action to curses
        add_filter( 'bulk_actions-edit-'.TWM_COURSE_TYPE, function( $bulk_actions ){
          $action = isset( $_REQUEST['post_status'] ) && $_REQUEST['post_status'] == 'trash' ? array() : array( self::$action => __( 'Export', TWM_TD ) );
          return array_merge( $bulk_actions, $action );
        }, 9999 );

        //  handle bulk action
        add_filter( 'handle_bulk_actions-edit-'.TWM_COURSE_TYPE, array( __CLASS__, 'handler' ), 10, 3 );

        //  admin notice
        //  if error then creating zip archive
        add_action( 'admin_notices', array( __CLASS__, 'notice' ) );
      }

      /**
       * show notice on dashboard
       * 
       * @since 1.0.34
       * 
       */
      public static function notice()
      {
        if( !isset( $_REQUEST[ self::$action.'-notice-status' ] ) )
          return;

        echo sprintf( '<div id="message" class="%s fade"><p><strong>%s</strong></p></div>', htmlspecialchars( $_REQUEST[ self::$action.'-notice-status' ] ), htmlspecialchars( $_REQUEST[ self::$action.'-notice-message' ] ) );
      }

      /**
       * handler for bulk action
       * 
       * @param  string $redirect_to
       * @param  string $action
       * @param  array  $post_ids
       * 
       * @since 1.0.34
       * 
       */
      public static function handler( $redirect_to, $action, $post_ids )
      {
        if( $action != self::$action )
          return $redirect_to;

        if( empty( $post_ids ) )
          return self::export_notice_url_with_text( $redirect_to, __( 'You have not selected any courses.', TWM_TD ) );

        $folder = wp_upload_dir();
        $path = $folder['basedir'].'/'.self::$archive_name;
        Directory::_instance( self::$base_folder_name );

        self::$zip = new \ZipArchive();
        
        $res = self::$zip->open( $path, \ZipArchive::CREATE );

        if( $res !== TRUE )
          return self::export_notice_url_with_text( $redirect_to, __( 'Error create ZIP archive with export data.', TWM_TD ) );
        
        foreach( $post_ids as $id ):
          $xml  =   new \SimpleXMLElement( '<xml/>' );
          $mw   =   $xml->addChild( str_replace( '-', '_', TWM_TD ) );

          //  set MW system data
          $mw->addAttribute( 'date_create', current_time( 'mysql', 1 ) );
          $mw->addAttribute( 'version', TWM_VERSION );
          
          $course_node = Data::element_data_to_xml( $id, $mw );
          
          self::get_child_data_to_xml( twmshp_get_modules_by_course( $id ), $course_node, 'module' );
          self::$zip->addFromString( Directory::get_current( 'info.xml', true ), $xml->asXML() );

          unset($xml);

          Directory::level_down();
        endforeach;

        self::$zip->close();

        Directory::_destroy();
        
        ob_clean();
        
        header( 'Content-type: application/zip' ); 
        header( 'Content-disposition: attachment; filename='.basename( self::$archive_name ) );
        header( 'Content-Length:'.filesize( $path ) );

        readfile( $path );
        unlink( $path );

        self::$zip = NULL;

        exit();
      }

      /**
       * generate xml data for each CPT element
       * 
       * @param  array            $elements
       * @param  SimpleXMLElement $node 
       * @param  string           $type     
       * 
       * @since 1.0.34
       * 
       */
      protected static function get_child_data_to_xml( $elements, $node, $type )
      {
        if( empty( $elements ) )
          return;

        $wrapper_node = $node->addChild( $type.'s' );

        foreach( $elements as $element )
        {
          $child_node = Data::element_data_to_xml( $element->ID, $wrapper_node );

          if( $type != 'lesson' )
            self::get_child_data_to_xml( twmshp_get_lessons_by_module( $element->ID ), $child_node, 'lesson' );

          unset( $child_node );

          Directory::level_down();
        }

        unset( $wrapper_node );
      }

      /**
       * get field value for element by field settings
       * 
       * @param  WP_Post $element
       * @param  array $field
       * 
       * @return string
       *
       * @since  1.0.34
       * 
       */
      public static function get_field_value( $element, $field )
      {
        $element = (array)$element;

        if( !isset( $field['type'] ) || $field['type'] == 'system' )
          return htmlspecialchars( $element[ $field[ 'key' ] ] );

        $value = get_post_meta( $element['ID'], \MemberWunder\DataFields::field_key( $field ), true );
        
        if( !in_array( $field['type'], array( 'meta_image', 'meta_featured' ) ) )
        {
          if( !is_array( $value ) )
            return htmlspecialchars( $value );
          else{
            if( isset( $value['salespage_text'] ) )
              unset( $value['salespage_text'] );

            if( isset( $value['advantages'] ) )
              unset( $value['advantages'] );

            return htmlspecialchars( serialize( $value ) );
          }
        }

        if( $field['type'] == 'meta_image' && !empty( $value ) )
        {
          $title = basename( $value );
          
          self::$zip->addFromString( Directory::get_current( $title, true ), file_get_contents( $value ) );

          return htmlspecialchars( $title );
        }
        
        if( $field['type'] == 'meta_featured' )
        {
          if( empty( $value ) )
            return '';

          $path = get_attached_file( $value );
          
          $title = basename( $path );

          self::$zip->addFile( $path, Directory::get_current( $title, true ) );
          return htmlspecialchars( $title );
        }

        return '';
      }

      /**
       * generate url with notice data
       * 
       * @param  string $base_url 
       * @param  string $message  
       * @param  string $type     
       * 
       * @return string
       *
       * @since  1.0.34
       * 
       */
      private static function export_notice_url_with_text( $base_url, $message = '', $type = 'error' )
      {
        $base_url = wp_parse_url( $base_url );
        $query = isset( $base_url['query'] ) ? wp_parse_args( $base_url['query'] ) : array();

        $query[ self::$action.'-notice-status' ]   = $type;
        $query[ self::$action.'-notice-message' ]  = $message;

        return add_query_arg( $query, $base_url['path'] );
      }

      private function __clone() {}

      public static function _instance()
      {
        if ( NULL === self::$_instance)
          self::$_instance = new self();

        return self::$_instance;
      }
    }