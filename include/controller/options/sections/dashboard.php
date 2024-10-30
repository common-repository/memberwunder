<?php
    namespace MemberWunder\Controller\Options\Sections;

    class Dashboard extends Section
    {
      /**
       * key of section
       * 
       * @var string
       *
       * @since  1.0.34.2
       * 
       */
      protected static $key = 'dashboard';

      /**
       * order for loading
       * 
       * @var integer
       *
       * @since  1.0.34.2
       * 
       */
      protected static $order = 20;

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
        return __( 'Dashboard', TWM_TD );
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
                        'key'     =>  'dashboard_title',
                        'label'   =>  __( 'Site title', TWM_TD ),
                        'attr'    =>  array(
                                          'type'            =>  'text',
                                          'class'           =>  'js-twm-tooltip regular-text', 
                                          'tooltip'         =>  __( 'Write down a welcome message as e.g. “Welcome to MemberWunder”', TWM_TD )
                                      ),
                        'default' =>  __( 'Welcome to MemberWunder', TWM_TD ),
                      ),
                  array(
                        'key'     =>  'dashboard_subtitle',
                        'label'   =>  __( 'Subtitle', TWM_TD ),
                        'attr'    =>  array(
                                          'type'            =>  'text',
                                          'class'           =>  'js-twm-tooltip regular-text', 
                                          'tooltip'         =>  __( 'Write down a slogan as e.g. “best Wordpress membership plugin”.', TWM_TD )
                                      ),
                        'default' =>  __( 'Simply build a membership community', TWM_TD ),
                      ),
                  array(
                        'key'     =>  'dashboard_description',
                        'label'   =>  __( 'Description', TWM_TD ),
                        'attr'    =>  array(
                                            'type'          =>  'wysiwyg',
                                            'id'            =>  'dashboard_description',
                                            'class'         =>  'js-twm-tooltip',
                                            'tooltip'       =>  __( 'Fill in an individual description of your membership area here.', TWM_TD ),
                                            'wysiwyg'       =>  array( 'editor_height' => 200, 'media_buttons' => false )
                                        ),
                        'default' =>  __( 'The MemberWunder Plugin is the simplest way to build a membership community, get paying customers and returning revenue. Directly after setting up the plugin, you have you own membership area like this here. You can upload your own course videos or use our whitelabel courses. Just try it - it has never been easier before.', TWM_TD ),
                      ),
                  array(
                        'key'     =>  'dashboard_background_image',
                        'label'   =>  __( 'Background image', TWM_TD ),
                        'attr'    =>  array(
                                            'type'          =>  'image',
                                            'class'         =>  'js-twm-tooltip js-twmshp__image_upload_value regular-text', 
                                            'tooltip'       => __( 'Here you can upload your background image for the “hero-header” section. Ideal size 1000x1000px.', TWM_TD )
                                      ),
                        'default' =>  '',
                      ),
                  array(
                        'key'     =>  'dashboard_notice_text',
                        'label'   =>  __( 'Notice text', TWM_TD ),
                        'attr'    =>  array(
                                            'type'          =>  'wysiwyg',
                                            'id'            =>  'dashboard_notice_text',
                                            'class'         =>  'js-twm-tooltip',
                                            'tooltip'       =>  __( 'Fill in any text or call-to-action here. Useful for important messages addressing your subscribers.', TWM_TD ),
                                            'wysiwyg'       =>  array( 'editor_height' => 200, 'media_buttons' => false  )
                                        ),
                        'default' =>  __( 'First time here? Find out more about the MemberWunder plugin, watch our explainer videos and high quality courses, where we explain how you can build your own online imperium with this plugin → <a target="_blank" href="https://memberwunder.com">memberwunder.com</a>', TWM_TD ),
                      ),
                  array(
                        'key'     =>  'dashboard_notice_image',
                        'label'   =>  __( 'Notice image', TWM_TD ),
                        'attr'    =>  array(  
                                            'type'          =>  'image',
                                            'class'         =>  'js-twm-tooltip js-twmshp__image_upload_value regular-text', 
                                            'tooltip'       =>  __( 'Upload an image to be shown next to the Notice text here.', TWM_TD )
                                        ),
                        'default' =>  TWM_ASSETS_URL.'/css/images/jakob-4.png',
                      ),
                    );
      }
    }