<?php
  if ( !twmshp_get_option('ds24_api_key'))
    echo '<div style="color:red">' . sprintf('<strong>%s</strong>', __('Attention. Digistore24 API key should be with write access!', TWM_TD)) . '</div>';

  $args = array(
                'name'          =>  $name,
                'value'         =>  $value, 
                'id'            =>  $id, 
              );

  foreach( array( 'class', 'tooltip', 'dependet', 'description', 'name', 'value', 'id' ) as $key )
    if( isset( $$key ) )
      $args[ $key ] = $$key;

  twm_get_template_part( 'settings/fields/text', $args );
?>