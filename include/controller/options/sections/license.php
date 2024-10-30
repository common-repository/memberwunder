<?php
    namespace MemberWunder\Controller\Options\Sections;

    class License extends Section
    {
      /**
       * key of section
       * 
       * @var string
       *
       * @since  1.0.34.2
       * 
       */
      protected static $key = 'license';

      /**
       * order for loading
       * 
       * @var integer
       *
       * @since  1.0.34.2
       * 
       */
      protected static $order = 45;

      /**
       * get label for section
       * 
       * @return string
       *
       * @since  1.0.34.2
       * 
       */
      public static function get_label() 
      {
        return __( 'License', TWM_TD );
      }

      /**
       * filter sections array
       *
       * @param  array
       * 
       * @return array
       *
       * @since  1.0.34.2
       * 
       */
      public static function section_filter( $sections )
      {
        if( !twm_is_pro() )
          unset( $sections[ self::$key ] );

        return $sections;
      }

      /**
       * return array of fields
       * 
       * @return array
       *
       * @since  1.0.34.2
       * 
       */
      protected static function fields()
      {
        return array(
                    array(
                        'key'     =>  'license_key',
                        'label'   =>  __( 'License key', TWM_TD ),
                        'attr'    =>  array(
                                          'type'    =>  'license',
                                          'class'   =>  'js-twm-tooltip large-text', 
                                          'cols'    =>  40,
                                          'rows'    =>  5,
                                          'tooltip' =>  __( 'To use the plugin properly, please fill in your “MemberWunder license key”. If you want to know where to find it, visit https://memberwunder.com/hilfe', TWM_TD )
                                      ),
                        'default' =>  '',
                      ),
                    );
      }
    }