<?php
    namespace MemberWunder\Controller\Options\Sections;

    class General extends Section
    {
      /**
       * key of section
       * 
       * @var string
       *
       * @since  1.0.34.2
       * 
       */
      protected static $key = 'general';

      /**
       * order for loading
       * 
       * @var integer
       *
       * @since  1.0.34.2
       * 
       */
      protected static $order = 0;

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
        return __( 'General', TWM_TD );
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
                        'key'     =>  'baseurl',
                        'label'   =>  __( 'Base url', TWM_TD ),
                        'attr'    =>  array(
                                            'type'          => 'full_link',
                                            'class'         => 'js-twm-tooltip regular-text', 
                                            'base'          => get_bloginfo( 'url' ),
                                            'tooltip'       => __( 'Here you can change the website link to access your membership area.', TWM_TD ),
                                            'description'   => __( 'Set the URL where your membership area will be available.<br/><strong>*Caution*:</strong> Your membership area will be visible as soon as you have inserted your license key.', TWM_TD ),
                                        ),
                        'default' =>  __( '/members', TWM_TD ),
                      ),
                      array(
                        'key'     =>  'allow_registration',
                        'label'   =>  __( 'Allow registration', TWM_TD ),
                        'attr'    =>  array(
                                            'type'          =>  'checkbox',
                                            'class'         => 'js-twm-tooltip', 
                                            'tooltip'       => __( 'Choose Yes to enable interested users to create a free account.', TWM_TD ) 
                                        ),
                        'default' =>  TRUE,
                      ),
                      array(
                        'key'     =>  'hide_unavailable_modules',
                        'label'   =>  __( 'Allow to hide unavailable modules and lessons', TWM_TD ),
                        'attr'    =>  array(
                                            'type'          =>  'checkbox',
                                            'class'         =>  'regular-text', 
                                            'id'            =>  'hide_unavailable_modules'
                                        ),
                        'default' =>  FALSE,
                      ),
                      array(
                        'key'     =>  'disallow_visit',
                        'label'   =>  __( 'Disallow visit <pre>/wp-admin</pre> for non privileged users', TWM_TD ),
                        'attr'    =>  array(
                                            'type'          =>  'select',
                                            'multiple'      =>  true,
                                            'options'       =>  call_user_func( function(){
                                                                    global $wp_roles;
                                                                    
                                                                    $roles = array();
                                                                    
                                                                    if( $wp_roles === NULL )
                                                                      return $roles;

                                                                    foreach( $wp_roles->roles as $key => $role )
                                                                        if( !in_array( $key, array( 'editor', 'administrator' ) ) )
                                                                        $roles[$key] = $role['name'];
                                                                    
                                                                    return $roles;
                                                                } ),
                                            'id'            =>  'disallow_visit',
                                            'class'         =>  'js-twm-tooltip', 
                                            'tooltip'       =>  __( 'Prevent unauthorized users from logging into or visiting the backend of your domain.', TWM_TD )
                                          ),
                        'default' =>  '',
                      ),
                    );
      }
    }