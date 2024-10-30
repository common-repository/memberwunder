<?php
  foreach( array( 'class', 'tooltip', 'dependet', 'description', 'name', 'value', 'id' ) as $key )
    if( isset( $$key ) )
      $args[ $key ] = $$key;

  twm_get_template_part( 'settings/fields/textarea', $args );

  $domains = new \MemberWunder\Services\Domains();
  if (!$domains->checkCurrentDomain())
    echo '<div style="color:red">' . sprintf( '<strong>%s</strong>', __( 'ERROR', TWM_TD ) ) . ' ' . implode( '<br>', $domains->getErrors() ) . '</div>';
?>