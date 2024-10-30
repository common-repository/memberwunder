<?php
    namespace MemberWunder\Controller\Options\Sections;

    class Digistore24 extends Section
    {
      /**
       * key of section
       * 
       * @var string
       *
       * @since  1.0.34.2
       * 
       */
      protected static $key = 'digistore24';

      /**
       * order for loading
       * 
       * @var integer
       *
       * @since  1.0.34.2
       * 
       */
      protected static $order = 30;

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
        return __( 'Digistore24', TWM_TD );
      }

      /**
       * filter sections array
       *
       * @param  array
       * 
       * @return array
       *
       * @since  1.0.34.2
       * 
       */
      public static function section_filter( $sections )
      {
        if( !twm_is_pro() )
        {
          unset( $sections[ self::$key ] );
        }else
          {
            //it is impposible to set DS24 without license key
            $license_key = twmshp_get_option('license_key');
            if( empty( $license_key ) && array_key_exists( self::$key, $sections ) )
                unset( $sections[ self::$key ] );                
            
            if( !isset( $_GET['key'] ) )
            {
              //it is impposible to set DS24 with invalid license key
              $domains = new \MemberWunder\Services\Domains();
              if(!empty($license_key) && array_key_exists( self::$key, $sections))
                  if( !$domains->checkCurrentDomain() )
                      unset( $sections[ self::$key ] ); 
            } 
          }
          
        return $sections;
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
                          'key'     =>  'ds24_api_key',
                          'label'   =>  __( 'Digistore24 API key', TWM_TD ),
                          'attr'    =>  array(
                                            'type'    =>  'digistore24',
                                            'class'   =>  'js-twm-tooltip regular-text', 
                                            'tooltip' =>  __( 'Fill in you Digistore24 API key here. If you want to know where to find it, visit https://memberwunder.com/hilfe', TWM_TD )
                                        ),
                          'default' =>  '',
                        ),
                    array(
                          'key'     =>  'subject',
                          'label'   =>  __( 'Subject (new user)', TWM_TD ),
                          'attr'    =>  array(
                                            'type'    =>  'text',
                                            'class'   =>  'regular-text'
                                        ),
                          'default' =>  __( 'Welcome to MemberWunder', TWM_TD ),
                        ),
                    array(
                          'key'     =>  'purchase_email',
                          'label'   =>  __( 'Purchase email (new user)', TWM_TD ),
                          'attr'    =>  array(
                                            'type'    =>  'wysiwyg',
                                            'class'   =>  'js-twm-tooltip', 
                                            'tooltip' =>  __( 'This email will automatically be sent to customers after every purchase of a membership product. You can individually change the content of this mail.', TWM_TD ),
                                            'wysiwyg' =>  array( 'editor_height' => 400 ),
                                            'description' =>  '<div class="ms-desc ms-desc-wide">%user_id% - '.__( 'User id', TWM_TD ).'<br>%login% - '.__( 'User login', TWM_TD ).'<br>%email% - '.__('Email', TWM_TD).'<br>%first_name% - '.__('First name', TWM_TD).'<br>%last_name% - '.__('Last name', TWM_TD).'<br>%course_name% - '.__('Course name', TWM_TD).'<br>%url% - '.twmshp_get_dashboard_url().'<br>%date% - '.__('Current date', TWM_TD).'</div>'
                                        ),
                          'default' =>  __( "Hi %first_name%,\n\nCongratuliations! Your membership account is ready to go. To get access, please go to <a href=\"%url%\">%url%</a> and log in with these credentials:\n\nemail: %email”\npassword: %password%\n\nI hope you enjoy %course_name%!\n\nAll the best,\n\nJohn Doe", TWM_TD ),
                        ),
                    array(
                          'key'     =>  'subject_existing_user',
                          'label'   =>  __( 'Subject (existing user)', TWM_TD ),
                          'attr'    =>  array(
                                            'type'    =>  'text',
                                            'class'   =>  'regular-text'
                                        ),
                          'default' =>  __( 'Welcome to MemberWunder', TWM_TD ),
                        ),
                    array(
                          'key'     =>  'purchase_email_existing_user',
                          'label'   =>  __( 'Purchase email (existing user)', TWM_TD ),
                          'attr'    =>  array(
                                            'type'    =>  'wysiwyg',
                                            'class'   => 'js-twm-tooltip', 
                                            'tooltip' => __( 'This email will automatically be sent to customers after every purchase of a membership product. You can individually change the content of this mail.', TWM_TD ),
                                            'wysiwyg' =>  array( 'editor_height' => 400 ),
                                            'description' =>  '<div class="ms-desc ms-desc-wide">%user_id% - '.__( 'User id', TWM_TD ).'<br>%login% - '.__( 'User login', TWM_TD ).'<br>%email% - '.__('Email', TWM_TD).'<br>%first_name% - '.__('First name', TWM_TD).'<br>%last_name% - '.__('Last name', TWM_TD).'<br>%course_name% - '.__('Course name', TWM_TD).'<br>%url% - '.twmshp_get_dashboard_url().'<br>%date% - '.__('Current date', TWM_TD).'</div>'
                                        ),
                          'default' =>  __( "Hi %first_name%,\n\nCongratuliations! You have successfully purchased %course_name%! To get access, please go to <a href=\"%url%\">%url%</a> and log in with your email and password. In case you forgot your password, please click on “forgot password”.\n\nI hope you enjoy %course_name%!\n\nAll the best,\n\nJane Doe", TWM_TD ),
                        )
                    );
      }
    }