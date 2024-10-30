<?php
    namespace MemberWunder\Controller\Options\Sections;

    class Analytics extends Section
    {
      /**
       * key of section
       * 
       * @var string
       *
       * @since  1.0.34.2
       * 
       */
      protected static $key = 'analytics';

      /**
       * order for loading
       * 
       * @var integer
       *
       * @since  1.0.34.2
       * 
       */
      protected static $order = 35;

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
        return __( 'Analytics', TWM_TD );
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
                        'key'     =>  'header_analytics_code',
                        'label'   =>  __( 'Header analytics code', TWM_TD ),
                        'attr'    =>  array(
                                            'class'         =>  'regular-text', 
                                            'type'          =>  'textarea',
                                            'id'            =>  'header_analytics_code',
                                            'rows'          =>  10
                                        ),
                        'default' =>  '',
                      ),
                    array(
                        'key'     =>  'footer_analytics_code',
                        'label'   =>  __( 'Footer analytics code', TWM_TD ),
                        'attr'    =>  array(
                                            'class'         =>  'regular-text', 
                                            'type'          =>  'textarea',
                                            'id'            =>  'footer_analytics_code',
                                            'rows'          =>  10
                                        ),
                        'default' =>  '',
                      ),
                    );
      }
    }