<?php
    namespace MemberWunder\Controller\Options;

    class Colors 
    {
      /**
       * set default colors for custom theme
       * 
       * @return array
       *
       * @since  1.0.28.10
       * 
       */
      public static function default_colors()
      {
        return array(
                    'clr_body_bg' => array(
                                            'label'     =>  __( 'Main body background', TWM_TD ),
                                            'image'     =>  'body-background.png',
                                            'hint'      =>  __( 'Change the main color of the background here.', TWM_TD ),
                                            'default'   =>  '#bd40bd',
                                            'key'       =>  'clrOkTextColor',
                                        ),
                    'clr_text' => array(
                                            'label'     =>  __( 'Text color', TWM_TD ),
                                            'hint'      =>  __( 'Change the color of the text here', TWM_TD ),
                                            'default'   => '#353434',
                                            'key'       =>  'clrText',
                                        ),
                    'clr_top_text' => array(
                                            'label'     =>  __( 'Menu item text color', TWM_TD ),
                                            'hint'      =>  __( 'Change the text color of menu items here', TWM_TD ),
                                            'image'     =>  'menu-color.png',
                                            'default'   =>  '#4e81d9',
                                            'key'       =>  'clrTopText',
                                        ),
                    'clr_link' => array(
                                            'label'     =>  __( 'Active menu item text color', TWM_TD ),
                                            'hint'      =>  __( 'Change the text color of menu items when the cursor hovers over the element', TWM_TD ),
                                            'image'     =>  'menu-color.png',
                                            'default'   =>  '#d63eb5',
                                            'key'       =>  'clrLink',
                                        ),
                    'clr_ibbg' => array(
                                            'label'     =>  __( 'Headline background color', TWM_TD ),
                                            'hint'      =>  __( 'Change the background color of the box around the headline of a page', TWM_TD ),
                                            'image'     =>  'promoblock-background.png',
                                            'default'   =>  '#d63e3e',
                                            'key'       =>  'clrIBbg',
                                        ),
                    'clr_ibtext' => array(
                                            'label'     =>  __( 'Headline text color', TWM_TD ),
                                            'hint'      =>  __( 'Change the text color of headlines', TWM_TD ),
                                            'image'     =>  'promoblock-text-color.png',
                                            'default'   =>  '#ebc5d0',
                                            'key'       =>  'clrIBtext',
                                        ),
                    'clr_desc_table_bg' => array(
                                            'label'     =>  __( 'Sidebar background color', TWM_TD ),
                                            'hint'      =>  __( 'Change the background color of the sidebar, which contains the meta-information about courses / lessons', TWM_TD ),
                                            'image'     =>  'metabox-background.png',
                                            'default'   =>  '#5aad65',
                                            'key'       =>  'clrDescTableBg',
                                        ),
                    'clr_desc_text_color' => array(
                                            'label'     =>  __( 'Sidebar text color', TWM_TD ),
                                            'hint'      =>  __( 'Change the text color of the sidebar, which contains the meta-informations about courses/lessons', TWM_TD ),
                                            'image'     =>  'metabox-text-color.png',
                                            'default'   =>  '#383d8f',
                                            'key'       =>  'clrDescTableTh',
                                        ),
                    'clr_desc_table_ico' => array(
                                            'label'     =>  __( 'Sidebar icon color', TWM_TD ),
                                            'hint'      =>  __( 'Change the color of items in the sidebar which contains the meta-information about courses / lessons', TWM_TD ),
                                            'image'     =>  'metabox-icon-color.png',
                                            'default'   =>  '#f0ec13',
                                            'key'       =>  'clrDescTableIco',
                                        ),
                    'clr_cource_bg' => array(
                                            'label'     =>  __( 'Overlay box background', TWM_TD ),
                                            'hint'      =>  __( 'Change the background overlay color of the main boxes of courses / lessons', TWM_TD ),
                                            'image'     =>  'box-background-color.png',
                                            'default'   =>  '#916724',
                                            'key'       =>  'clrCourceBg',
                                        ),
                    'clr_cource_bg_act' => array(
                                            'label'     => 'Box active background',
                                            'default'   => '#54422c',
                                            'key'       =>  'clrCourceBgAct',
                                        ),
                    'clr_cource_text' => array(
                                            'label'     =>  __( 'Course / lesson box text color', TWM_TD ),
                                            'hint'      =>  __( 'Change the text color of the main boxes of courses / lessons', TWM_TD ),
                                            'default'   =>  '#7a80d9',
                                            'image'     =>  'box-text-color.png',
                                            'key'       =>  'clrCourceText',
                                        ),
                    'clr_foot_bg' => array(
                                            'label'     =>  __( 'Footer menu background', TWM_TD ),
                                            'hint'      =>  __( 'Change the background color of the footer menu here', TWM_TD ),
                                            'image'     =>  'footer-background.png', 
                                            'default'   =>  '#74bbe8',
                                            'key'       =>  'clrFootBg',
                                        ),
                    'clr_foot_text' => array(
                                            'label'     =>  __( 'Footer menu item text color', TWM_TD ),
                                            'hint'      =>  __( 'Change the text color of footer menu items here', TWM_TD ),
                                            'image'     =>  'footer-text-color.png',
                                            'default'   =>  '#5661f7',
                                            'key'       =>  'clrFootText',
                                        ),
                    'clr_ok' => array(
                                            'label'     =>  __( 'Button background color', TWM_TD ),
                                            'hint'      =>  __( 'Change the background color of buttons here', TWM_TD ),
                                            'image'     =>  'button.png',
                                            'default'   =>  '#126e30',
                                            'key'       =>  'clrOk',
                                        )
                );
      }

      /**
       * converted colors data(DB) to array of less variables
       * 
       * @return array
       *
       * @since  1.0.28.10
       * 
       */
      public static function to_less_variables()
      {
        $values = twmshp_get_option('colors');

        $variables    = array();
        $colorScheme  = self::getColorScheme();
        $default      = array( 'pathToImages' => "'".TWM_TEMPLATES_URL.'/public/css/img/'."'" );

        if( $colorScheme == 'custom' )
          foreach( self::default_colors() as $key => $color )
            $variables[ $color['key'] ] = $values[ $key ];

        $default[ 'colorScheme' ] = !is_file( \MemberWunder\Services\Styles::get_path_to_less( 'color/color_'.$colorScheme ) ) ? 0 : $colorScheme;
      
        return array_merge( $default, $variables );
      }

      /**
       * get color scheme 
       * 
       * @return string
       *
       * @since 1.0.36.1
       * 
       */
      public static function getColorScheme()
      {
        $template = twmshp_get_template();

        return $template == 'custom' ? $template: ( substr( $template, 0, 5 ) === 'theme' ? substr( $template, 5 ) : 0 );
      }
    }