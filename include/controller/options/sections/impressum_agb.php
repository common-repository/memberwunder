<?php
    namespace MemberWunder\Controller\Options\Sections;

    class Impressum_agb extends Section
    {
      /**
       * key of section
       * 
       * @var string
       *
       * @since  1.0.34.2
       * 
       */
      protected static $key = 'impressum_agb';

      /**
       * order for loading
       * 
       * @var integer
       *
       * @since  1.0.34.2
       * 
       */
      protected static $order = 55;

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
        return __( 'Impressum & AGB', TWM_TD );
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
                        'key'     =>  'impressum_content',
                        'label'   =>  __( 'Impressum', TWM_TD ),
                        'attr'    =>  array(
                                          'type'          =>  'wysiwyg',
                                          'wysiwyg'       =>  array( 'editor_height' => 400, 'media_buttons' => false  )
                                      ),
                        'default' =>  '',
                      ),
                  array(
                        'key'     =>  'agb_content',
                        'label'   =>  __( 'AGB', TWM_TD ),
                        'attr'    =>  array(
                                          'type'          =>  'wysiwyg',
                                          'wysiwyg'       =>  array( 'editor_height' => 400, 'media_buttons' => false  )
                                      ),
                        'default' =>  '',
                      ),
                    );
      }
    }