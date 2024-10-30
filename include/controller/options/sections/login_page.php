<?php
    namespace MemberWunder\Controller\Options\Sections;

    class Login_page extends Section
    {
      /**
       * key of section
       * 
       * @var string
       *
       * @since  1.0.34.2
       * 
       */
      protected static $key = 'login_page';

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
      public static function get_label() 
      {
        return __( 'Login page', TWM_TD );
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
                        'key'     =>  'show_logo_in_main',
                        'label'   =>  __( 'Allow to show logo on login page', TWM_TD ),
                        'attr'    =>  array(
                                        'type'    =>  'checkbox',
                                        'class'   =>  'js-twm-tooltip', 
                                        'tooltip' =>  __( 'Choose Yes to show logo on login page', TWM_TD )
                                      ),
                        'default' =>  TRUE,
                      ),
                  array(
                        'key'     =>  'login_page_background_image',
                        'label'   =>  __( 'Background image', TWM_TD ),
                        'attr'    =>  array(
                                        'class'   =>  'regular-text js-twmshp__image_upload_value',
                                        'type'    =>  'image',
                                      ),
                        'default' =>  '',
                      ),
                  array(
                        'key'     =>  'login_layot_type',
                        'label'   =>  __( 'Layout type', TWM_TD ),
                        'attr'    =>  array(
                                            'type'          =>  'select',
                                            'options'       =>  array( 
                                                                    'simple'    => __( 'Simple', TWM_TD ), 
                                                                    'advansed'  => __( 'Advansed', TWM_TD ) 
                                                                    ),
                                            'id'            =>  'login_layot_type',
                                        ),
                        'default' =>  'simple',
                      ),
                  array(
                        'key'     =>  'login_advansed_layot_type',
                        'label'   =>  __( 'Courses visibility settings', TWM_TD ),
                        'attr'    =>  array(
                                            'type'          =>  'select',
                                            'options'       =>  array( 
                                                                    1 => __( "Don't show courses", TWM_TD ), 
                                                                    2 => __( "Show only free courses", TWM_TD ), 
                                                                    3 => __( "Show all courses", TWM_TD ), 
                                                                    5 => __( "Show only selected courses", TWM_TD ), 
                                                                    ),
                                            'id'            =>  'login_advansed_layot_type',
                                            'dependet'      =>  array(
                                                                    'field' =>  'login_layot_type',
                                                                    'value' =>  'advansed'
                                                                    )
                                        ),
                        'default' =>  3,
                      ),
                  array(
                        'key'     =>  'login_page_title',
                        'label'   =>  __( 'Title', TWM_TD ),
                        'attr'    =>  array(
                                          'type'            =>  'text'
                                      ),
                        'default' =>  __( 'Welcome to MemberWunder', TWM_TD ),
                      ),
                  array(
                        'key'     =>  'login_page_subtitle',
                        'label'   =>  __( 'Subtitle', TWM_TD ),
                        'attr'    =>  array(
                                          'type'            =>  'text'
                                      ),
                        'default' =>  __( 'Simply build a membership community', TWM_TD ),
                      ),
                  array(
                        'key'     =>  'login_page_description',
                        'label'   =>  __( 'Description', TWM_TD ),
                        'attr'    =>  array(
                                            'type'          =>  'wysiwyg',
                                            'id'            =>  'login_page_description',
                                            'wysiwyg'       =>  array( 'editor_height' => 200, 'media_buttons' => false  )
                                        ),
                        'default' =>  __( 'The MemberWunder Plugin is the simplest way to build a membership community, get paying customers and returning revenue. Directly after setting up the plugin, you have a ready to go membership area. You can upload your own course videos or use our whitelabel courses. Just try it - it has never been easier before.', TWM_TD ),
                      )
                    );
      }
    }