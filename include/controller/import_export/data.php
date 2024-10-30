<?php
    namespace MemberWunder\Controller\ImportExport;

    class Data
    {
      private static $_instance = NULL;

      private function __construct()
      {
        \MemberWunder\Helpers\General::load( 'controller/import_export/directory' );
        \MemberWunder\Helpers\General::load( 'controller/import_export/export' );
        \MemberWunder\Helpers\General::load( 'controller/import_export/import' );
      }

      /**
       * get elements args from xml
       *
       * @param  SimpleXMLElement $node
       * @param  string           $type
       * 
       * @return array
       */
      public static function element_data_from_xml( $node, $type )
      {
        $args = array();
        $node = (array)$node;

        $fields = self::get_fields( $type );
        foreach( $fields as $field )
        {
          $value = (string)$node[ $field['key'] ];
          $value = \MemberWunder\Helpers\General::is_serialized( $value ) ? unserialize( $value ) : $value;

          if( $field['key'] == 'post_name' )
            Directory::level_up( $value );
          
          if( !isset( $field['type'] ) || $field['type'] == 'system' )
            $args[ $field['key'] ] = $value;
          else{            
            if( in_array( $field['type'], array( 'meta_featured', 'meta_image' ) ) && !empty( $value ) )
              $value = \MemberWunder\Helpers\General::upload_image_to_media(  Directory::get_current( $value ) );
            
            if( $field['type'] == 'meta_image' )
              $value = wp_get_attachment_url( $value ); 

            $args[ 'meta_input' ][ \MemberWunder\DataFields::field_key( $field ) ] = $value;
          }
          unset( $value );
        }

        return $args;
      }

      /**
       * generate xml for element
       * course, modul, lesson
       * 
       * @param  int              $id   
       * @param  SimpleXMLElement &$node
       *
       * @return SimpleXMLElement &child_node
       * 
       * @since 1.0.34
       * 
       */
      public static function element_data_to_xml( $id, &$node )
      {
        $element = get_post( $id );

        $child_node = $node->addChild( self::get_node_type( $element->post_type ) );

        Directory::level_up( $element->post_name );

        foreach( self::get_fields( self::get_node_type( $element->post_type ) ) as $field ):
          $field_node = $child_node->addChild( $field['key'], Export::get_field_value( $element, $field ) );

          unset( $field_node );
        endforeach;

        return $child_node;
      }

      /**
       * get node type by post type
       * 
       * @param  string $type
       * 
       * @return string
       *
       * @since  1.0.34
       * 
       */
      protected static function get_node_type( $type )
      {
        switch ( $type ) 
        {
          case TWM_COURSE_TYPE:
            return 'course';
          case TWM_MODULE_TYPE:
            return 'module';
          default:
            return 'lesson';
        }
      }

      private function __clone() {}

      /**
       * get fields list by post type
       *
       * @param string $type
       * 
       * @return array
       *
       * @since  1.0.34
       * @since  1.0.35 added loading fields from \MemberWunder\DataFields
       * 
       */
      protected static function get_fields( $type )
      {
        return array_filter(
                          \MemberWunder\DataFields::fields( $type ),
                          function( $element ){
                            return isset( $element['import-export'] ) && !$element['import-export'] ? FALSE : TRUE;
                          });
      }

      public static function _instance()
      {
        if ( NULL === self::$_instance)
          self::$_instance = new self();

        return self::$_instance;
      }
    }