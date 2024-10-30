<?php
  namespace MemberWunder\Helpers\Template;

  class General
  {
    /**
     * show copyright link with format
     * 
     * @param  string $fomat 
     * 
     * @since 1.0.29
     * 
     */
    public static function copyright( $fomat = '%s' )
    {
        if( twmshp_get_option( 'hide_memberwunder_link' ) )
            return;

        $url = twmshp_get_option( 'memberwunder_digistore_id' ) != '' ? sprintf( 'http://go.%s.166313.15631.digistore24.com/CAMPAIGNKEY', twmshp_get_option( 'memberwunder_digistore_id' ) ) : 'http://memberwunder.com/';

        echo sprintf( $fomat, sprintf( '<a href="%s" target="_blank">'.__( 'Memberwunder.com', TWM_TD ).'</a>', $url ) );
    }

    /**
     * show validation message
     * 
     * @param  boolen $status  
     * @param  string $message
     * 
     * @since  1.0.29
     * 
     */
    public static function show_validation_message( $status, $message )
    {
      \MemberWunder\Helpers\View::get_template_part( 
                                                    'blocks/general/message', 
                                                    array( 
                                                        'status'            => $status,
                                                        'message'           => $message 
                                                        ) 
                                                    );
    }
  }
