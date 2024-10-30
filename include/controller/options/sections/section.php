<?php
    namespace MemberWunder\Controller\Options\Sections;

    class Section 
    {
      /**
       * key of section
       * 
       * @var string
       *
       * @since  1.0.34.2
       * 
       */
      protected static $key = '';

      /**
       * order for loading
       * 
       * @var integer
       *
       * @since  1.0.34.2
       * 
       */
      protected static $order = 10;

      /**
       * get label for section
       * 
       * @return string
       *
       * @since  1.0.34.2
       * 
       */
      public static function get_label() {}

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
            return array();
      }

      /**
       * return array of fields for section
       *
       * @param  array $fields
       * 
       * @return array
       *
       * @since  1.0.34.2
       * 
       */
      public static function get_fields( $fields )
      {
        return array_merge( $fields, array( static::$key => static::fields() ) );
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
        return $sections;
      }

      /**
       * get order for section
       * 
       * @return string
       *
       * @since  1.0.34.2
       * 
       */
      public static function get_order()
      {
        return static::$order;
      }

      /**
       * get key for section
       * 
       * @return string
       *
       * @since  1.0.34.2
       * 
       */
      public static function get_key()
      {
        return static::$key;
      }
    }