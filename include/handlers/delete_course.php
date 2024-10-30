<?php
    namespace MemberWunder\Handlers;

    class DeleteCourse
    {
      private static $_instance = NULL;

      private function __construct()
      {
        add_action( 'before_delete_post', array( __CLASS__, 'before_delete_course' ), 100 );
      }

      private function __clone() {}

      public static function _instance()
      {
        if ( NULL === self::$_instance)
          self::$_instance = new self();

        return self::$_instance;
      }

      /**
       * for each course remove all modules and lessons
       * 
       * @param  int $postid
       * 
       * @return string
       *
       * @since  1.0.34
       * 
       */
      public static function before_delete_course( $postid )
      {
        global $post_type;   

        if ( $post_type != TWM_COURSE_TYPE ) 
          return;

        self::remove( twmshp_get_modules_by_course( $postid ) );
      }
      
      /**
       * remove elements and childs(lessons)
       * 
       * @param  array $elements
       * 
       * @since 1.0.34
       * 
       */
      protected static function remove( $elements )
      {
        if( empty( $elements ) )
          return;

        foreach( $elements as $element ):
          if( $element->post_type != TWM_LESSONS_TYPE )
            self::remove( twmshp_get_lessons_by_module( $element->ID ) );

          wp_delete_post( $element->ID, true );
        endforeach;
      }
    }