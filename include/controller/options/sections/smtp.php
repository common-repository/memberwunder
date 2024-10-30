<?php
    namespace MemberWunder\Controller\Options\Sections;

    class Smtp extends Section
    {
      /**
       * key of section
       * 
       * @var string
       *
       * @since  1.0.34.2
       * 
       */
      protected static $key = 'smtp';

      /**
       * order for loading
       * 
       * @var integer
       *
       * @since  1.0.34.2
       * 
       */
      protected static $order = 50;

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
        return __( 'Smtp', TWM_TD );
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
                        'key'     =>  'smtp',
                        'label'   =>  '',
                        'attr'    =>  array(
                                          'type'    =>  'smtp',
                                          'class'   =>  'hidden-th'
                                      ),
                        'default' =>  '',
                      ),
                    );
      }
    }