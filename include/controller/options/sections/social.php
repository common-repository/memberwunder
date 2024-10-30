<?php
    namespace MemberWunder\Controller\Options\Sections;

    class Social extends Section
    {
      /**
       * key of section
       * 
       * @var string
       *
       * @since  1.0.34.2
       * 
       */
      protected static $key = 'social';

      /**
       * order for loading
       * 
       * @var integer
       *
       * @since  1.0.34.2
       * 
       */
      protected static $order = 15;

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
        return __( 'Social media', TWM_TD );
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
                          'key'     =>  'social_header',
                          'label'   =>  __( 'Social networks section header', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          =>  'text',
                                            'class'         =>  'js-twm-tooltip regular-text', 
                                            'type'          =>  'text',
                                            'tooltip'       =>  __( 'Here you add the headline for your social share elements. This headline will be above the social share elements. f.e.: follow us', TWM_TD ),
                                        ),
                          'default' =>  '',
                        ),
                    array(
                          'key'     =>  'social',
                          'label'   =>  __( 'Social media', TWM_TD ),
                          'attr'    =>  array(
                                          'type'          =>  'social',
                                        ),
                          'default' =>  array(
                                          'label'       =>  array( __( 'Max Mustermann', TWM_TD ) ),
                                          'description' =>  array( __( 'CEO', TWM_TD ) ),
                                          'url'         =>  array( 'https://www.facebook.com/' ),
                                          'image'       =>  array( TWM_ASSETS_URL.'/css/images/crp-75x75.jpg' ),
                                        ),
                        ),
                    );
      }
    }