<?php
    namespace MemberWunder\Controller\Options\Sections;

    class Autoresponders extends Section
    {
      /**
       * key of section
       * 
       * @var string
       *
       * @since  1.0.34.2
       * 
       */
      protected static $key = 'autoresponders';

      /**
       * order for loading
       * 
       * @var integer
       *
       * @since  1.0.34.2
       * 
       */
      protected static $order = 40;

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
        return __( 'E-mail marketing', TWM_TD );
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
        $fields = array();
        foreach(   
                array( 
                      array( 
                            'key'     => 'klick_tipp_allow',             
                            'label'   => __( 'Allow Klick-Tipp', TWM_TD ),              
                            'type'    => 'checkbox',
                            'default' =>  FALSE
                          ),
                      array( 
                            'key'     => 'klick_tipp_api_key',           
                            'label'   => __( 'Klick-Tipp API key', TWM_TD ),            
                            'type'    => 'text',
                            'default' =>  '',
                            'dependet'=>  'klick_tipp_allow'
                          ),
                      array( 
                            'key'     => 'mailchimp_allow',              
                            'label'   => __( 'Allow MailChimp', TWM_TD ),               
                            'type'    => 'checkbox',
                            'default' =>  FALSE 
                          ),
                      array( 
                            'key'     => 'mailchimp_api_key',            
                            'label'   => __( 'MailChimp API key', TWM_TD ),             
                            'type'    => 'text',
                            'default' =>  '',
                            'dependet'=>  'mailchimp_allow'
                          ),
                      array( 
                            'key'     => 'mailchimp_list_id',            
                            'label'   => __( 'MailChimp List ID', TWM_TD ),             
                            'type'    => 'text',
                            'default' => '',
                            'dependet'=> 'mailchimp_allow' 
                          ),
                      array( 
                            'key'     => 'activecampaign_allow',         
                            'label'   => __( 'Allow ActiveCampaign', TWM_TD ),          
                            'type'    => 'checkbox',
                            'default' =>  FALSE
                          ),
                      array( 
                            'key'     => 'activecampaign_api_url',       
                            'label'   => __( 'ActiveCampaign API URL', TWM_TD ),        
                            'type'    => 'text',
                            'default' => '',
                            'dependet'=>  'activecampaign_allow' 
                          ),
                      array( 
                            'key'     => 'activecampaign_api_key',       
                            'label'   => __( 'ActiveCampaign API key', TWM_TD ),        
                            'type'    => 'text',
                            'default' => '',
                            'dependet'=>  'activecampaign_allow'  
                          ),
                      array( 
                            'key'     => 'getresponse_allow',            
                            'label'   => __( 'Allow GetResponse', TWM_TD ),             
                            'type'    => 'checkbox',
                            'default' =>  FALSE 
                          ),
                      array( 
                            'key'     => 'getresponse_api_key',          
                            'label'   => __( 'GetResponse API key', TWM_TD ),           
                            'type'    => 'text',
                            'default' => '',
                            'dependet'=>  'getresponse_allow'   
                          ),
                      array(
                            'key'     => 'getresponse_campaign_name',    
                            'label'   => __( 'GetResponse Campaign Name', TWM_TD ),     
                            'type'    => 'text',
                            'default' => '',
                            'dependet'=>  'getresponse_allow'  
                          ),
                    )
                as $field ):
            $attr = array(
                        'key'     =>  $field['key'],
                        'label'   =>  $field['label'],
                        'attr'    =>  array(
                                            'class'         => 'regular-text', 
                                            'type'          => $field['type'],
                                            'for_pro'       =>  TRUE,
                                            'description'   => $field['type'] == 'text' ? sprintf( '<p><a target="_blank" href="https://memberwunder.com/mitglieder/courses/memberwunder/roadmap-zum-erfolg/new-lesson-32/">%s</a> %s</p>', __( 'Here you can find instruction', TWM_TD ), __( 'how to set this value', TWM_TD ) ) : ''
                                        ),
                        'default' =>  $field['default'],
                      );
            if( isset( $field['dependet'] ) )
              $attr['attr']['dependet'] = array(
                                                'field' =>  $field['dependet'],
                                                'value' =>  1
                                                );

            $fields[] = $attr;
          endforeach;
        return $fields;
      }
    }