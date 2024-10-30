<?php
    namespace MemberWunder\Controller\Options\Sections;

    class Design extends Section
    {
      /**
       * key of section
       * 
       * @var string
       *
       * @since  1.0.34.2
       * 
       */
      protected static $key = 'design';

      /**
       * order for loading
       * 
       * @var integer
       *
       * @since  1.0.34.2
       * 
       */
      protected static $order = 5;

      /**
       * default settings for styles
       * 
       * @var array
       *
       * @since 1.0.16.4
       * 
       */
      public static $themes = array(
                                    array( 'theme0', '#3e74d7' ),
                                    array( 'theme1', '#075', true ),
                                    array( 'theme2', '#555', true ),
                                    array( 'theme3', '#075' ),
                                    array( 'theme4', '#d77971' ),
                                    array( 'theme5', '#ddd' ),
                                    array( 'theme6', '#555', true ),
                                    array( 'theme7', '#6d8b8d' ),
                                    array( 'theme8', '#90d7b7', true ),
                                    array( 'theme9', '#bacae4', true ),
                                    array( 'theme10', '#ce8031', true ),
                                    array( 'theme11', '#555' ),
                                    array( 'theme12', '#999', true ),
                                    array( 'theme13', '#ff0', true ),
                                    array( 'theme14', '#333', true ),
                                    array( 'theme15', '#99d194' ),
                                    array( 'theme16', '#78858e', true ),
                                    array( 'theme17', '#8e084f', true ),
                                    array( 'theme18', '#3c2e4f', true ),
                                    array( 'theme19', '#96979b', true ),
                                    array( 'theme20', '#96979b', true ),
                                    array( 'theme21', '#96979b', true ),
                                    array( 'custom', '' )
                                  );

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
        return __( 'Design', TWM_TD );
      }

      /**
       * generate code of hint for custom color scheme
       * 
       * @param  string $key
       * 
       * @return string
       *
       * @since 1.0.18.12
       * 
       */
      public static function template_color_hint( $key )
      {
          $colors = \MemberWunder\Controller\Options\Colors::default_colors();
          
          $option = $colors[ $key ];
          $string = isset( $option[ 'hint' ] ) ? '<p>'.$option[ 'hint' ].'</p>' : '';
          $string .= isset( $option[ 'image' ] ) ? '<img src="'.TWM_ASSETS_URL.'/dashboard/images/settings/hints/'.$option['image'].'" alt="'.$option[ 'label' ].'" />' : '';
          
          return $string;
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
                          'key'     =>  'logo',
                          'label'   =>  __( 'Logo', TWM_TD ),
                          'attr'    =>  array(
                                              'type'    =>  'image',
                                              'class'   =>  'js-twm-tooltip regular-text js-twmshp__image_upload_value', 
                                              'tooltip' =>  __( 'Here you can upload your individual logo for the header section of your membership area. Optimal Size 130x30px.', TWM_TD )
                                            ),
                          'default' =>  TWM_ASSETS_URL.'/css/images/logo.png',
                        ),
                  array(
                          'key'     =>  'logo_footer',
                          'label'   =>  __( 'Footer Logo', TWM_TD ),
                          'attr'    =>  array(  
                                              'type'    =>  'image',
                                              'class'   =>  'js-twm-tooltip regular-text js-twmshp__image_upload_value', 
                                              'tooltip' =>  __( 'Here you can upload your individual logo for the footer section of your membership area. Optimal Size 130x30px.', TWM_TD )
                                            ),
                          'default' =>  TWM_ASSETS_URL.'/css/images/logo.png',
                        ),
                  array(
                          'key'     =>  'favicon',
                          'label'   =>  __( 'Favicon', TWM_TD ),
                          'attr'    =>  array(
                                              'type'    =>  'image',
                                              'class'   =>  'regular-text js-twmshp__image_upload_value'
                                            ),
                          'default' =>  '',
                        ),
                  array(
                          'key'     =>  'registration_background',
                          'label'   =>  __( 'Background image for registration page', TWM_TD ),
                          'attr'    =>  array(
                                            'class'         => 'js-twm-tooltip regular-text js-twmshp__image_upload_value',
                                            'id'            => 'registration_background', 
                                            'type'          => 'image',
                                            'tooltip'       => __( 'Here you can set the background image for your registration page of your membership area.', TWM_TD ),
                                        ),
                          'default' =>  '',
                        ),
                  array(
                          'key'     =>  'template',
                          'label'   =>  __( 'Template', TWM_TD ),
                          'attr'    =>  array(
                                            'class'         => 'js-twm-tooltip', 
                                            'type'          => 'template',
                                            'tooltip'       => __( 'Here you can change coloring of your membership area.', TWM_TD ),
                                        ),
                          'default' =>  'theme0',
                        ),
                  array(
                          'key'     =>  'hide_memberwunder_link',
                          'label'   =>  __( 'Hide MemberWunder Link', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          => 'checkbox',
                                            'for_pro'       =>  TRUE,
                                        ),
                          'default' =>  FALSE,
                        ),
                  array(
                          'key'     =>  'memberwunder_digistore_id',
                          'label'   =>  __( 'Digistore24 ID', TWM_TD ),
                          'attr'    =>  array(
                                            'class'         =>  'regular-text', 
                                            'type'          =>  'text',
                                            'dependet'      =>  array(
                                                                    'field' =>  'hide_memberwunder_link',
                                                                    'value' =>  0
                                                                    ),
                                            'for_pro'       =>  TRUE,
                                        ),
                          'default' =>  '',
                        ),
                  array(
                          'key'     =>  'hide_color_overlay',
                          'label'   =>  __( 'Remove image color overlay', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          =>  'checkbox',
                                            'id'            =>  'hide_color_overlay',
                                            'class'         =>  'js-twm-tooltip', 
                                            'tooltip'       =>  __( 'Choose this option if you want to show your course and module images without color overlay. Caution: Texts could be hardly readable without it!', TWM_TD )
                                        ),
                          'default' =>  FALSE,
                        ),
                  array(
                          'key'     =>  'custom_email_templates',
                          'label'   =>  __( 'Enable custom email templates', TWM_TD ),
                          'attr'    =>  array(
                                            'type'          =>  'checkbox',
                                            'id'            =>  'custom_email_templates',
                                            'for_pro'       =>  TRUE,
                                        ),
                          'default' =>  TRUE,
                        ),
                  array(
                          'key'     =>  'custom_css',
                          'label'   =>  __('Custom CSS', TWM_TD),
                          'attr'    =>  array(
                                            'type'          =>  'textarea'
                                        ),
                          'default' =>  '',
                        ),
                    );
      }
    }