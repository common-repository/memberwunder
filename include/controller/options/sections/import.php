<?php
    namespace MemberWunder\Controller\Options\Sections;

    class Import extends Section
    {
      /**
       * key of section
       * 
       * @var string
       *
       * @since  1.0.34.2
       * 
       */
      protected static $key = 'import';

      /**
       * order for loading
       * 
       * @var integer
       *
       * @since  1.0.34.2
       * 
       */
      protected static $order = 60;

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
        return __( 'Import', TWM_TD );
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
                          'key'     =>  'import_block',
                          'label'   =>  __( 'Zip archive with data', TWM_TD ),
                          'attr'    =>  array(
                                            'class'         =>  'regular-text', 
                                            'type'          =>  'import_block',
                                            'for_pro'       =>  TRUE,
                                        ),
                          'default' =>  '',
                        ),
                    );
      }
    }